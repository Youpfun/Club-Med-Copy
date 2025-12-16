<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\Reservation;
use App\Models\Partenaire;
use App\Mail\StayConfirmationMail;
use App\Mail\PartnerConfirmationMail;
use App\Mail\ResortConfirmationMail;

class StayConfirmationController extends Controller
{
    public function showConfirmationForm($numreservation)
    {
        $reservation = Reservation::with(['resort', 'user', 'chambres', 'chambres.typechambre', 'transport', 'activites', 'activites.activite'])
            ->findOrFail($numreservation);

        if (!$this->isVenteMember()) {
            abort(403, 'Seul le service vente peut accéder à cette page');
        }

        $partenaires = collect();
        foreach ($reservation->activites as $activity) {
            if ($activity->activite) {
                $activityPartners = DB::table('fourni')
                    ->join('partenaire', 'fourni.numpartenaire', '=', 'partenaire.numpartenaire')
                    ->where('fourni.numactivite', $activity->activite->numactivite)
                    ->select('partenaire.*')
                    ->get();
                
                $partenaires = $partenaires->merge($activityPartners);
            }
        }

        $partenaires = $partenaires->unique('numpartenaire');

        $partenairesStatus = DB::table('reservation_activite')
            ->join('partenaire', 'reservation_activite.numpartenaire', '=', 'partenaire.numpartenaire')
            ->where('reservation_activite.numreservation', $numreservation)
            ->whereNotNull('reservation_activite.numpartenaire')
            ->select(
                'partenaire.numpartenaire',
                'partenaire.nompartenaire',
                'reservation_activite.partenaire_validation_status',
                'reservation_activite.partenaire_validated_at'
            )
            ->get();

        $allPartnersAccepted = $partenairesStatus->isNotEmpty() && 
                               $partenairesStatus->every(function($p) {
                                   return $p->partenaire_validation_status === 'accepted';
                               });
        
        $hasRefusedPartners = $partenairesStatus->contains(function($p) {
            return $p->partenaire_validation_status === 'refused';
        });

        $resortValidated = $reservation->resort_validation_status === 'accepted';
        $resortRefused = $reservation->resort_validation_status === 'refused';

        return view('stay-confirmation.form', [
            'reservation' => $reservation,
            'partenaires' => $partenaires,
            'partenairesStatus' => $partenairesStatus,
            'allPartnersAccepted' => $allPartnersAccepted,
            'hasRefusedPartners' => $hasRefusedPartners,
            'resortValidated' => $resortValidated,
            'resortRefused' => $resortRefused,
        ]);
    }

    public function sendConfirmation(Request $request, $numreservation)
    {
        if (!$this->isVenteMember()) {
            abort(403, 'Seul le service vente peut confirmer les séjours');
        }

        $reservation = Reservation::findOrFail($numreservation);

        if ($reservation->resort_validation_status !== 'accepted') {
            if ($reservation->resort_validation_status === 'refused') {
                return back()->with('error', 'Impossible de confirmer : le resort a refusé cette réservation.');
            }
            return back()->with('error', 'Impossible de confirmer : le resort n\'a pas encore validé cette réservation.');
        }

        $partenairesStatus = DB::table('reservation_activite')
            ->where('numreservation', $numreservation)
            ->whereNotNull('numpartenaire')
            ->get();

        if ($partenairesStatus->isNotEmpty()) {
            $hasRefused = $partenairesStatus->contains(function($p) {
                return $p->partenaire_validation_status === 'refused';
            });

            $hasPending = $partenairesStatus->contains(function($p) {
                return $p->partenaire_validation_status === 'pending';
            });

            if ($hasRefused) {
                return back()->with('error', 'Impossible de confirmer : un ou plusieurs partenaires ont refusé les dates.');
            }

            if ($hasPending) {
                return back()->with('error', 'Impossible de confirmer : tous les partenaires n\'ont pas encore validé les dates.');
            }
        }

        $request->validate([
            'confirmation_message' => 'nullable|string|max:1000',
            'notify_resort' => 'boolean',
            'notify_partenaires' => 'boolean',
        ]);

        $reservation = Reservation::with(['resort', 'user', 'chambres', 'chambres.typechambre', 'transport', 'activites', 'activites.activite'])
            ->findOrFail($numreservation);

        $notifyResort = $request->input('notify_resort', true);
        $notifyPartenaires = $request->input('notify_partenaires', true);
        $confirmationMessage = $request->input('confirmation_message');

        try {
            if ($reservation->user && $reservation->user->email) {
                Mail::to($reservation->user->email)
                    ->send(new StayConfirmationMail($reservation, 'client'));
            }

            if ($notifyResort && $reservation->resort) {
                $resortEmail = $this->getResortEmail($reservation->resort->numresort);
                if ($resortEmail) {
                    Mail::to($resortEmail)
                        ->send(new ResortConfirmationMail($reservation));
                }
            }

            if ($notifyPartenaires) {
                $partenairesEmails = DB::table('reservation_activite')
                    ->join('partenaire', 'reservation_activite.numpartenaire', '=', 'partenaire.numpartenaire')
                    ->where('reservation_activite.numreservation', $numreservation)
                    ->whereNotNull('reservation_activite.numpartenaire')
                    ->distinct()
                    ->select('partenaire.numpartenaire', 'partenaire.nompartenaire', 'partenaire.emailpartenaire')
                    ->get();

                foreach ($partenairesEmails as $partenaireData) {
                    if ($partenaireData->emailpartenaire) {
                        $partenaire = (object)[
                            'numpartenaire' => $partenaireData->numpartenaire,
                            'nompartenaire' => $partenaireData->nompartenaire,
                            'emailpartenaire' => $partenaireData->emailpartenaire,
                        ];
                        
                        Mail::to($partenaireData->emailpartenaire)
                            ->send(new PartnerConfirmationMail($reservation, $partenaire));
                    }
                }
            }

            $reservation->update([
                'statut' => 'confirmee',
            ]);

            DB::table('reservation_confirmations')->insert([
                'numreservation' => $numreservation,
                'user_id' => Auth::id(),
                'notify_resort' => $notifyResort,
                'notify_partenaires' => $notifyPartenaires,
                'confirmation_message' => $confirmationMessage,
                'confirmed_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('vente.dashboard')
                ->with('success', 'Séjour confirmé et emails envoyés avec succès!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'envoi des emails: ' . $e->getMessage());
        }
    }

    private function isVenteMember()
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }
        return strpos(strtolower($user->role ?? ''), 'vente') !== false;
    }

    private function getResortEmail($numresort)
    {
        $resort = DB::table('resort')->where('numresort', $numresort)->first();
        
        if ($resort && isset($resort->emailresort)) {
            return $resort->emailresort;
        }

        return config('mail.from.address');
    }
}


<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\Reservation;
use App\Models\Partenaire;
use App\Mail\StayConfirmationMail;

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

        return view('stay-confirmation.form', [
            'reservation' => $reservation,
            'partenaires' => $partenaires,
        ]);
    }

    public function sendConfirmation(Request $request, $numreservation)
    {
        if (!$this->isVenteMember()) {
            abort(403, 'Seul le service vente peut confirmer les séjours');
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
                        ->send(new StayConfirmationMail($reservation, 'resort'));
                }
            }

            if ($notifyPartenaires) {
                $partenairesEmails = DB::table('fourni')
                    ->join('partenaire', 'fourni.numpartenaire', '=', 'partenaire.numpartenaire')
                    ->join('activitealacarte', 'fourni.numactivite', '=', 'activitealacarte.numactivite')
                    ->join('reservation_activite', function($join) use ($numreservation) {
                        $join->on('reservation_activite.numactivite', '=', 'activitealacarte.numactivite')
                            ->where('reservation_activite.numreservation', $numreservation);
                    })
                    ->distinct('partenaire.numpartenaire')
                    ->select('partenaire.numpartenaire', 'partenaire.nompartenaire', 'partenaire.emailpartenaire')
                    ->get();

                foreach ($partenairesEmails as $partenaire) {
                    if ($partenaire->emailpartenaire) {
                        Mail::to($partenaire->emailpartenaire)
                            ->send(new StayConfirmationMail($reservation, 'partenaire'));
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

            return redirect('/mes-reservations')
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


<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\Reservation;
use App\Models\Resort;
use App\Mail\ResortValidationMail;

class AlternativeResortController extends Controller
{
    /**
     * Affiche la page de réponse du client pour le resort alternatif
     */
    public function show(Request $request, $token)
    {
        $reservation = Reservation::with(['resort', 'user', 'alternativeResort'])
            ->where('alternative_resort_token', $token)
            ->first();

        if (!$reservation) {
            return view('client.alternative-resort-error', [
                'message' => 'Token invalide. Ce lien n\'existe pas ou a expiré.'
            ]);
        }

        if ($reservation->alternative_resort_token_expires_at < now()) {
            return view('client.alternative-resort-error', [
                'message' => 'Ce lien a expiré. Veuillez contacter notre service commercial.'
            ]);
        }

        if ($reservation->alternative_resort_responded_at) {
            return view('client.alternative-resort-error', [
                'message' => 'Vous avez déjà répondu à cette proposition. Statut actuel : ' . 
                    ($reservation->alternative_resort_status === 'accepted' ? 'Acceptée' : 'Refusée')
            ]);
        }

        $originalResort = $reservation->resort;
        $alternativeResort = $reservation->alternativeResort;

        // Si l'action est passée en paramètre, on traite directement
        if ($request->has('action')) {
            return $this->respond($request, $token);
        }

        return view('client.alternative-resort', compact('reservation', 'originalResort', 'alternativeResort', 'token'));
    }

    /**
     * Traite la réponse du client
     */
    public function respond(Request $request, $token)
    {
        $action = $request->input('action');
        
        if (!in_array($action, ['accept', 'refuse'])) {
            return redirect()->back()->with('error', 'Action invalide.');
        }

        $reservation = Reservation::with(['resort', 'user', 'alternativeResort'])
            ->where('alternative_resort_token', $token)
            ->first();

        if (!$reservation) {
            return view('client.alternative-resort-error', [
                'message' => 'Token invalide.'
            ]);
        }

        if ($reservation->alternative_resort_token_expires_at < now()) {
            return view('client.alternative-resort-error', [
                'message' => 'Ce lien a expiré. Veuillez contacter notre service commercial.'
            ]);
        }

        if ($reservation->alternative_resort_responded_at) {
            return view('client.alternative-resort-error', [
                'message' => 'Vous avez déjà répondu à cette proposition.'
            ]);
        }

        $status = $action === 'accept' ? 'accepted' : 'refused';

        DB::beginTransaction();
        try {
            $reservation->update([
                'alternative_resort_status' => $status,
                'alternative_resort_responded_at' => now(),
            ]);

            if ($status === 'accepted') {
                // Changer le resort de la réservation
                $newResort = $reservation->alternativeResort;
                $oldResortId = $reservation->numresort;

                $reservation->update([
                    'numresort' => $newResort->numresort,
                    'resort_validation_status' => 'pending',
                    'resort_validated_at' => null,
                    'resort_validation_token_used_at' => null,
                ]);

                // Envoyer un email de validation au nouveau resort
                $resortToken = (string) Str::uuid();
                $expiresAt = now()->addDays(3);

                $reservation->update([
                    'resort_validation_token' => $resortToken,
                    'resort_validation_token_expires_at' => $expiresAt,
                ]);

                $resortEmail = $newResort->emailresort ?? config('mail.from.address');
                $resortLink = url('/resort/validate/' . $resortToken);

                try {
                    Mail::to($resortEmail)->send(new ResortValidationMail(
                        $reservation->fresh()->load(['resort', 'user', 'activites.activite', 'chambres.typechambre']),
                        $newResort,
                        $resortLink
                    ));
                    \Log::info('Email validation envoyé au nouveau resort', [
                        'numreservation' => $reservation->numreservation,
                        'resort_email' => $resortEmail,
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Erreur envoi email au nouveau resort: ' . $e->getMessage());
                }
            }

            DB::commit();

            return view('client.alternative-resort-result', [
                'status' => $status,
                'reservation' => $reservation,
                'alternativeResort' => $reservation->alternativeResort,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur traitement réponse alternative: ' . $e->getMessage());
            return view('client.alternative-resort-error', [
                'message' => 'Une erreur est survenue. Veuillez réessayer ou contacter notre service commercial.'
            ]);
        }
    }
}

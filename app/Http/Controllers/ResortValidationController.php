<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\Reservation;
use App\Mail\PartnerValidationMail;

class ResortValidationController extends Controller
{
    public function show($token)
    {
        $reservation = Reservation::with(['resort', 'user', 'chambres.typechambre', 'activites.activite'])
            ->where('resort_validation_token', $token)
            ->first();

        if (!$reservation) {
            return view('resort.validation-error', [
                'message' => 'Token de validation invalide ou expiré.'
            ]);
        }

        if ($reservation->resort_validation_token_expires_at < now()) {
            return view('resort.validation-error', [
                'message' => 'Ce lien de validation a expiré.'
            ]);
        }

        if ($reservation->resort_validation_token_used_at) {
            return view('resort.validation-error', [
                'message' => 'Ce lien a déjà été utilisé. Statut actuel : ' . $reservation->resort_validation_status
            ]);
        }

        return view('resort.validation', compact('reservation', 'token'));
    }

    public function respond(Request $request, $token)
    {
        $request->validate([
            'action' => 'required|in:accept,refuse',
            'comment' => 'nullable|string|max:1000',
        ]);

        $reservation = Reservation::with(['resort', 'activites.activite'])
            ->where('resort_validation_token', $token)
            ->first();

        if (!$reservation) {
            return redirect()->route('home')->with('error', 'Token invalide.');
        }

        if ($reservation->resort_validation_token_expires_at < now()) {
            return view('resort.validation-error', [
                'message' => 'Ce lien de validation a expiré.'
            ]);
        }

        if ($reservation->resort_validation_token_used_at) {
            return view('resort.validation-error', [
                'message' => 'Ce lien a déjà été utilisé.'
            ]);
        }

        $action = $request->input('action');
        $comment = $request->input('comment');
        $status = $action === 'accept' ? 'accepted' : 'refused';

        $reservation->update([
            'resort_validation_status' => $status,
            'resort_validated_at' => now(),
            'resort_validation_token_used_at' => now(),
        ]);

        if ($status === 'accepted') {
            $this->sendPartnerValidationEmails($reservation);
        }

        return view('resort.validation-result', [
            'status' => $status,
            'reservation' => $reservation,
            'comment' => $comment
        ]);
    }

    private function sendPartnerValidationEmails($reservation)
    {
        $partenairesData = DB::table('reservation_activite')
            ->join('partenaire', 'reservation_activite.numpartenaire', '=', 'partenaire.numpartenaire')
            ->where('reservation_activite.numreservation', $reservation->numreservation)
            ->whereNotNull('reservation_activite.numpartenaire')
            ->distinct()
            ->select('partenaire.*')
            ->get();

        foreach ($partenairesData as $partenaireData) {
            if ($partenaireData->emailpartenaire) {
                $token = Str::uuid()->toString();
                $expiresAt = now()->addDays(3);

                DB::table('reservation_activite')
                    ->where('numreservation', $reservation->numreservation)
                    ->where('numpartenaire', $partenaireData->numpartenaire)
                    ->update([
                        'validation_token' => $token,
                        'validation_token_expires_at' => $expiresAt,
                    ]);

                $tokenLink = url('/partner/validate/' . $token);

                Mail::to($partenaireData->emailpartenaire)
                    ->send(new PartnerValidationMail($reservation, $partenaireData, $tokenLink));
            }
        }
    }
}

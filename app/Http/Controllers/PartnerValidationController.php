<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Reservation;
use App\Models\ReservationActivite;

class PartnerValidationController extends Controller
{
    public function show($token)
    {
        $row = ReservationActivite::where('validation_token', $token)->first();
        if (!$row) {
            abort(404, 'Lien invalide');
        }

        if ($row->validation_token_expires_at && Carbon::parse($row->validation_token_expires_at)->isPast()) {
            abort(410, 'Lien expiré');
        }

        $reservation = Reservation::with(['resort', 'user', 'activites' => function ($q) use ($row) {
            $q->with('activite')->where('reservation_activite.numpartenaire', $row->numpartenaire);
        }])->find($row->numreservation);

        if (!$reservation) {
            abort(404, 'Réservation introuvable');
        }

        $partenaire = DB::table('partenaire')->where('numpartenaire', $row->numpartenaire)->first();

        return view('partner.validation', [
            'token' => $token,
            'reservation' => $reservation,
            'partenaire' => $partenaire,
        ]);
    }

    public function respond(Request $request, $token)
    {
        $action = $request->input('action'); // accept or refuse
        $row = ReservationActivite::where('validation_token', $token)->first();
        if (!$row) {
            abort(404, 'Lien invalide');
        }

        if ($row->validation_token_expires_at && Carbon::parse($row->validation_token_expires_at)->isPast()) {
            abort(410, 'Lien expiré');
        }

        if (!in_array($action, ['accept', 'refuse'])) {
            abort(400, 'Action invalide');
        }

        $status = $action === 'accept' ? 'accepted' : 'refused';

        ReservationActivite::where('numreservation', $row->numreservation)
            ->where('numpartenaire', $row->numpartenaire)
            ->update([
                'partenaire_validation_status' => $status,
                'partenaire_validated_at' => now(),
                'validation_token_used_at' => now(),
            ]);

        return view('partner.validation_result', [
            'status' => $status,
        ]);
    }
}

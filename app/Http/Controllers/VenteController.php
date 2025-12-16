<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Reservation;
use App\Models\ReservationRejection;

class VenteController extends Controller
{
    public function dashboard()
    {
        if (!Auth::user() || (strpos(strtolower(Auth::user()->role ?? ''), 'vente') === false)) {
            abort(403, 'Accès réservé au service vente');
        }

        $reservationsPendingConfirmation = Reservation::with(['resort', 'user', 'activites.activite'])
            ->whereIn('statut', ['en_attente', 'payee'])
            ->orderBy('datedebut', 'asc')
            ->paginate(15);

        foreach ($reservationsPendingConfirmation as $reservation) {
            $partenairesStatus = DB::table('reservation_activite')
                ->join('partenaire', 'reservation_activite.numpartenaire', '=', 'partenaire.numpartenaire')
                ->where('reservation_activite.numreservation', $reservation->numreservation)
                ->whereNotNull('reservation_activite.numpartenaire')
                ->select(
                    'partenaire.nompartenaire',
                    'reservation_activite.partenaire_validation_status',
                    'reservation_activite.partenaire_validated_at'
                )
                ->get();
            
            $reservation->partenaires_status = $partenairesStatus;
        }

        $confirmedIds = DB::table('reservation_confirmations')
            ->orderBy('confirmed_at', 'desc')
            ->limit(10)
            ->pluck('numreservation')
            ->toArray();

        $confirmedReservations = collect();
        foreach ($confirmedIds as $id) {
            $r = Reservation::with(['resort', 'user'])->find($id);
            if ($r) {
                $r->confirmed_at = DB::table('reservation_confirmations')
                    ->where('numreservation', $id)
                    ->orderBy('confirmed_at', 'desc')
                    ->value('confirmed_at');
                $confirmedReservations->push($r);
            }
        }

        $stats = [
            'total_pending' => Reservation::whereIn('statut', ['en_attente', 'payee'])->count(),
            'total_confirmed' => Reservation::where('statut', 'confirmee')->count(),
            'total_upcoming' => Reservation::where('statut', 'confirmee')
                ->where('datedebut', '>=', now())
                ->count(),
        ];

        return view('vente.dashboard', compact(
            'reservationsPendingConfirmation',
            'confirmedReservations',
            'stats'
        ));
    }

    public function showRejectForm($numreservation)
    {
        if (!Auth::user() || (strpos(strtolower(Auth::user()->role ?? ''), 'vente') === false)) {
            abort(403, 'Accès réservé au service vente');
        }

        $reservation = Reservation::with(['resort', 'user'])->findOrFail($numreservation);

        if ($reservation->statut === 'rejetee') {
            return redirect()->route('vente.dashboard')->with('error', 'Cette réservation a déjà été rejetée.');
        }

        return view('vente.reject-reservation', compact('reservation'));
    }

    public function rejectReservation(Request $request, $numreservation)
    {
        if (!Auth::user() || (strpos(strtolower(Auth::user()->role ?? ''), 'vente') === false)) {
            abort(403, 'Accès réservé au service vente');
        }

        $request->validate([
            'reason' => 'required|string|in:client_refused,new_resort_not_accepted,availability_issue,other',
        ]);

        $reservation = Reservation::findOrFail($numreservation);

        if ($reservation->statut === 'rejetee') {
            return back()->with('error', 'Cette réservation a déjà été rejetée.');
        }

        try {
            DB::beginTransaction();

            ReservationRejection::create([
                'numreservation' => $numreservation,
                'user_id' => Auth::id(),
                'rejection_reason' => $request->input('reason'),
                'rejected_at' => now(),
            ]);

            $reservation->update([
                'statut' => 'rejetee',
            ]);

            DB::commit();

            return redirect()->route('vente.dashboard')->with('success', "Réservation #{$numreservation} rejetée avec succès.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors du rejet de la réservation: ' . $e->getMessage());
        }
    }


}

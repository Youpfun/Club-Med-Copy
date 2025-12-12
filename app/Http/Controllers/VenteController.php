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

        $reservationsPendingConfirmation = Reservation::with(['resort', 'user', 'activites'])
            ->whereIn('statut', ['en_attente', 'payee'])
            ->orderBy('datedebut', 'asc')
            ->paginate(15);

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

    public function pendingPartnerValidation()
    {
        if (!Auth::user() || (strpos(strtolower(Auth::user()->role ?? ''), 'vente') === false)) {
            abort(403, 'Accès réservé au service vente');
        }

        $reservations = Reservation::with(['resort', 'user', 'activites', 'activites.activite'])
            ->where('statut', '!=', 'confirmee')
            ->whereHas('activites')
            ->orderBy('datedebut', 'asc')
            ->paginate(15);

        return view('vente.pending-partners', compact('reservations'));
    }

    public function showRejectForm($numreservation)
    {
        if (!Auth::user() || (strpos(strtolower(Auth::user()->role ?? ''), 'vente') === false)) {
            abort(403, 'Accès réservé au service vente');
        }

        $reservation = Reservation::with(['resort', 'user'])->findOrFail($numreservation);

        if ($reservation->statut === 'rejetee') {
            return redirect('/vente/dashboard')->with('error', 'Cette réservation a déjà été rejetée.');
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
            'notes' => 'nullable|string|max:1000',
            'refund_amount' => 'required|numeric|min:0',
        ]);

        $reservation = Reservation::findOrFail($numreservation);

        if ($reservation->statut === 'rejetee') {
            return back()->with('error', 'Cette réservation a déjà été rejetée.');
        }

        try {
            DB::beginTransaction();

            $refundAmount = $request->input('refund_amount');

            ReservationRejection::create([
                'numreservation' => $numreservation,
                'user_id' => Auth::id(),
                'reason' => $request->input('reason'),
                'notes' => $request->input('notes'),
                'refund_amount' => $refundAmount,
                'refund_status' => 'pending',
                'rejected_at' => now(),
            ]);

            $reservation->update([
                'statut' => 'rejetee',
            ]);

            DB::commit();

            return redirect('/vente/dashboard')->with('success', "Réservation #{$numreservation} rejetée et remboursement de {$refundAmount}€ enregistré. Statut: en attente de traitement.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors du rejet de la réservation: ' . $e->getMessage());
        }
    }
}

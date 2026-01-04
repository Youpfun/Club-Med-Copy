<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Reservation;
use App\Models\Resort;
use App\Models\ReservationRejection;
use Illuminate\Support\Facades\Mail;
use App\Mail\PartnerConfirmationMail;
use App\Mail\AlternativeResortProposalMail;
use App\Mail\ReservationRejectedMail;

class VenteController extends Controller
{
    public function dashboard()
    {
        if (!Auth::user() || (strpos(strtolower(Auth::user()->role ?? ''), 'vente') === false)) {
            abort(403, 'Accès réservé au service vente');
        }

        $reservationsPendingConfirmation = Reservation::with(['resort', 'user', 'activites.activite'])
            ->whereIn('statut', ['en_attente', 'payee', 'Confirmée'])
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

        // Récupérer les réservations dont le resort a refusé (nécessite proposition alternative)
        $reservationsResortRefused = Reservation::with(['resort', 'user', 'alternativeResort'])
            ->where('resort_validation_status', 'refused')
            ->whereIn('alternative_resort_status', ['none', 'refused'])
            ->whereNotIn('statut', ['rejetee', 'annulee'])
            ->orderBy('datedebut', 'asc')
            ->get();

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
            'total_pending' => Reservation::whereIn('statut', ['en_attente', 'payee', 'Confirmée'])->count(),
            'total_confirmed' => Reservation::where('statut', 'confirmee')->count(),
            'total_upcoming' => Reservation::where('statut', 'confirmee')
                ->where('datedebut', '>=', now())
                ->count(),
            'total_resort_refused' => $reservationsResortRefused->count(),
        ];

        return view('vente.dashboard', compact(
            'reservationsPendingConfirmation',
            'confirmedReservations',
            'reservationsResortRefused',
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

            // Envoyer un email au client pour l'informer du rejet
            $reservation->load(['resort', 'user']);
            if ($reservation->user && $reservation->user->email) {
                try {
                    Mail::to($reservation->user->email)->send(
                        new ReservationRejectedMail($reservation, $request->input('reason'))
                    );
                    \Log::info('Email de rejet envoyé au client', [
                        'numreservation' => $numreservation,
                        'client_email' => $reservation->user->email,
                        'reason' => $request->input('reason'),
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Erreur envoi email rejet au client: ' . $e->getMessage(), [
                        'numreservation' => $numreservation,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('vente.dashboard')->with('success', "Réservation #{$numreservation} rejetée avec succès.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors du rejet de la réservation: ' . $e->getMessage());
        }
    }

    public function confirmReservation($numreservation)
    {
        if (!Auth::user() || (strpos(strtolower(Auth::user()->role ?? ''), 'vente') === false)) {
            abort(403, 'Accès réservé au service vente');
        }

        $reservation = Reservation::with(['resort', 'user', 'activites'])->findOrFail($numreservation);

        $pending = DB::table('reservation_activite')
            ->where('numreservation', $numreservation)
            ->where('partenaire_validation_status', 'pending')
            ->count();

        if ($pending > 0) {
            return back()->with('error', 'Tous les partenaires n\'ont pas encore répondu.');
        }

        $reservation->update(['statut' => 'confirmee']);

        $partenaires = DB::table('reservation_activite')
            ->join('partenaire', 'reservation_activite.numpartenaire', '=', 'partenaire.numpartenaire')
            ->where('reservation_activite.numreservation', $numreservation)
            ->whereNotNull('reservation_activite.numpartenaire')
            ->distinct()
            ->select('partenaire.*')
            ->get();

        foreach ($partenaires as $partenaire) {
            try {
                Mail::to($partenaire->emailpartenaire)->send(new PartnerConfirmationMail($reservation, $partenaire));
            } catch (\Exception $e) {
                \Log::error('Envoi email confirmation partenaire échec: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Réservation confirmée et partenaires notifiés.');
    }

    /**
     * Affiche le formulaire pour proposer un resort alternatif
     */
    public function showProposeAlternativeForm($numreservation)
    {
        if (!Auth::user() || (strpos(strtolower(Auth::user()->role ?? ''), 'vente') === false)) {
            abort(403, 'Accès réservé au service vente');
        }

        $reservation = Reservation::with(['resort', 'user'])->findOrFail($numreservation);

        // Vérifier que le resort a bien refusé
        if ($reservation->resort_validation_status !== 'refused') {
            return redirect()->route('vente.dashboard')->with('error', 'Le resort n\'a pas refusé cette réservation.');
        }

        // Récupérer les resorts alternatifs disponibles (même pays ou similaires)
        $originalResort = $reservation->resort;
        $alternativeResorts = Resort::with(['pays', 'photos'])
            ->where('numresort', '!=', $originalResort->numresort)
            ->orderBy('nomresort')
            ->get();

        return view('vente.propose-alternative', compact('reservation', 'originalResort', 'alternativeResorts'));
    }

    /**
     * Envoie la proposition de resort alternatif au client
     */
    public function proposeAlternativeResort(Request $request, $numreservation)
    {
        if (!Auth::user() || (strpos(strtolower(Auth::user()->role ?? ''), 'vente') === false)) {
            abort(403, 'Accès réservé au service vente');
        }

        $request->validate([
            'alternative_resort_id' => 'required|exists:resort,numresort',
            'message' => 'nullable|string|max:2000',
        ]);

        $reservation = Reservation::with(['resort', 'user'])->findOrFail($numreservation);

        if ($reservation->resort_validation_status !== 'refused') {
            return back()->with('error', 'Le resort n\'a pas refusé cette réservation.');
        }

        $alternativeResort = Resort::findOrFail($request->alternative_resort_id);
        $originalResort = $reservation->resort;

        // Générer un token unique pour la réponse du client
        $token = Str::uuid()->toString();
        $expiresAt = now()->addDays(7);

        // Mettre à jour la réservation
        $reservation->update([
            'alternative_resort_id' => $alternativeResort->numresort,
            'alternative_resort_status' => 'proposed',
            'alternative_resort_proposed_at' => now(),
            'alternative_resort_token' => $token,
            'alternative_resort_token_expires_at' => $expiresAt,
            'alternative_resort_message' => $request->message,
            'alternative_proposed_by' => Auth::id(),
        ]);

        // Envoyer l'email au client
        $tokenLink = url('/client/alternative-resort/' . $token);

        try {
            Mail::to($reservation->user->email)->send(
                new AlternativeResortProposalMail($reservation, $originalResort, $alternativeResort, $tokenLink, $request->message)
            );
            \Log::info('Email proposition resort alternatif envoyé', [
                'numreservation' => $numreservation,
                'client_email' => $reservation->user->email,
                'alternative_resort' => $alternativeResort->nomresort,
            ]);

            return redirect()->route('vente.dashboard')->with('success', 
                "Proposition de resort alternatif envoyée au client pour la réservation #{$numreservation}."
            );
        } catch (\Exception $e) {
            \Log::error('Erreur envoi email proposition alternative: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de l\'envoi de l\'email: ' . $e->getMessage());
        }
    }
}

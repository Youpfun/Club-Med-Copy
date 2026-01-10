<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Reservation;
use App\Models\Resort;
use App\Models\Remboursement;
use Illuminate\Support\Facades\Mail;
use App\Mail\PartnerConfirmationMail;
use App\Mail\AlternativeResortProposalMail;
use App\Mail\ReservationRejectedMail;
use App\Mail\ActivityCancelledMail;
use App\Models\Activite;

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
                    'reservation_activite.partenaire_validated_at',
                    'reservation_activite.numactivite'
                )
                ->get();
            
            $reservation->partenaires_status = $partenairesStatus;
            
            // Compter les activites en attente de reponse partenaire
            $reservation->activites_pending_count = $partenairesStatus->where('partenaire_validation_status', 'pending')->count();
        }

        // Recuperer les reservations avec activites en attente de reponse partenaire (plus de 48h)
        $reservationsActivitiesPending = Reservation::with(['resort', 'user', 'activites.activite'])
            ->whereIn('statut', ['en_attente', 'payee', 'Confirmée'])
            ->whereHas('activites', function($q) {
                $q->whereNotNull('numpartenaire')
                  ->where('partenaire_validation_status', 'pending')
                  ->where('created_at', '<', now()->subHours(48));
            })
            ->orderBy('datedebut', 'asc')
            ->get();

        foreach ($reservationsActivitiesPending as $reservation) {
            $activitesPending = DB::table('reservation_activite')
                ->join('activite', 'reservation_activite.numactivite', '=', 'activite.numactivite')
                ->leftJoin('partenaire', 'reservation_activite.numpartenaire', '=', 'partenaire.numpartenaire')
                ->where('reservation_activite.numreservation', $reservation->numreservation)
                ->whereNotNull('reservation_activite.numpartenaire')
                ->where('reservation_activite.partenaire_validation_status', 'pending')
                ->where('reservation_activite.created_at', '<', now()->subHours(48))
                ->select(
                    'reservation_activite.*',
                    'activite.nomactivite',
                    'partenaire.nompartenaire'
                )
                ->get();
            
            $reservation->activites_pending = $activitesPending;
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
            'total_activities_pending' => $reservationsActivitiesPending->sum(fn($r) => $r->activites_pending->count()),
        ];

        return view('vente.dashboard', compact(
            'reservationsPendingConfirmation',
            'confirmedReservations',
            'reservationsResortRefused',
            'reservationsActivitiesPending',
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

            Remboursement::create([
                'numreservation' => $numreservation,
                'user_id' => Auth::id(),
                'montant' => $reservation->prixtotal ?? 0,
                'statut' => Remboursement::STATUT_EN_ATTENTE,
                'raison' => $request->input('reason'),
                'date_demande' => now(),
            ]);

            $reservation->update([
                'statut' => 'rejetee',
            ]);

            // Envoyer un email au client pour l'informer du rejet avec des alternatives
            $reservation->load(['resort', 'resort.pays', 'user']);
            if ($reservation->user && $reservation->user->email) {
                try {
                    // Récupérer des resorts alternatifs
                    $alternativeResorts = $this->getAlternativeResorts($reservation);
                    
                    Mail::to($reservation->user->email)->send(
                        new ReservationRejectedMail($reservation, $request->input('reason'), $alternativeResorts)
                    );
                    \Log::info('Email de rejet envoyé au client avec alternatives', [
                        'numreservation' => $numreservation,
                        'client_email' => $reservation->user->email,
                        'reason' => $request->input('reason'),
                        'nb_alternatives' => $alternativeResorts->count(),
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

    /**
     * Récupère des resorts alternatifs pour proposer au client
     */
    private function getAlternativeResorts($reservation)
    {
        $originalResort = $reservation->resort;
        
        if (!$originalResort) {
            return Resort::with(['pays'])->orderBy('nbtridents', 'desc')->limit(3)->get();
        }
        
        // D'abord, chercher des resorts dans le même pays
        $sameCountryResorts = Resort::with(['pays'])
            ->where('numresort', '!=', $originalResort->numresort)
            ->where('codepays', $originalResort->codepays)
            ->orderBy('nbtridents', 'desc')
            ->limit(3)
            ->get();
        
        // Si on a moins de 3 resorts du même pays, compléter avec d'autres
        if ($sameCountryResorts->count() < 3) {
            $otherResorts = Resort::with(['pays'])
                ->where('numresort', '!=', $originalResort->numresort)
                ->where('codepays', '!=', $originalResort->codepays)
                ->orderBy('nbtridents', 'desc')
                ->limit(3 - $sameCountryResorts->count())
                ->get();
            
            return $sameCountryResorts->concat($otherResorts);
        }
        
        return $sameCountryResorts;
    }

    /**
     * Affiche les activités d'une réservation pour gestion
     */
    public function showActivities($numreservation)
    {
        if (!Auth::user() || (strpos(strtolower(Auth::user()->role ?? ''), 'vente') === false)) {
            abort(403, 'Accès réservé au service vente');
        }

        $reservation = Reservation::with(['resort', 'user', 'activites.activite.typeActivite'])
            ->findOrFail($numreservation);

        return view('vente.manage-activities', compact('reservation'));
    }

    /**
     * Annule une activité spécifique d'une réservation
     */
    public function cancelActivity(Request $request, $numreservation, $numactivite)
    {
        if (!Auth::user() || (strpos(strtolower(Auth::user()->role ?? ''), 'vente') === false)) {
            abort(403, 'Accès réservé au service vente');
        }

        $reservation = Reservation::findOrFail($numreservation);

        try {
            DB::beginTransaction();

            // Trouver l'activité
            $activity = DB::table('reservation_activite')
                ->where('numreservation', $numreservation)
                ->where('numactivite', $numactivite)
                ->first();

            if (!$activity) {
                return back()->with('error', 'Activité non trouvée.');
            }

            // Récupérer les infos de l'activité pour l'email
            $activiteInfo = Activite::find($numactivite);
            $activityTotal = $activity->prix_unitaire * $activity->quantite;
            
            // Préparer les données pour l'email
            $cancelledActivities = [[
                'nom' => $activiteInfo->nomactivite ?? 'Activité',
                'quantite' => $activity->quantite,
                'prix_unitaire' => $activity->prix_unitaire,
                'total' => $activityTotal,
            ]];

            // Supprimer l'activité
            DB::table('reservation_activite')
                ->where('numreservation', $numreservation)
                ->where('numactivite', $numactivite)
                ->delete();

            // Mettre à jour le prix total de la réservation
            $newTotal = $reservation->prixtotal - $activityTotal;
            $reservation->update(['prixtotal' => max(0, $newTotal)]);

            DB::commit();

            // Envoyer l'email au client
            $reservation->load(['user', 'resort']);
            if ($reservation->user && $reservation->user->email) {
                try {
                    Mail::to($reservation->user->email)->send(
                        new ActivityCancelledMail($reservation, $cancelledActivities, $activityTotal, true)
                    );
                    \Log::info('Email annulation activité envoyé au client', [
                        'numreservation' => $numreservation,
                        'client_email' => $reservation->user->email,
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Erreur envoi email annulation activité: ' . $e->getMessage());
                }
            }

            \Log::info('Activité annulée par le service vente', [
                'numreservation' => $numreservation,
                'numactivite' => $numactivite,
                'montant_deduit' => $activityTotal,
                'user_id' => Auth::id(),
            ]);

            return back()->with('success', "L'activité a été annulée avec succès. Montant déduit : " . number_format($activityTotal, 2, ',', ' ') . " €");
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur annulation activité: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de l\'annulation de l\'activité: ' . $e->getMessage());
        }
    }

    /**
     * Annule toutes les activités d'une réservation
     */
    public function cancelAllActivities(Request $request, $numreservation)
    {
        if (!Auth::user() || (strpos(strtolower(Auth::user()->role ?? ''), 'vente') === false)) {
            abort(403, 'Accès réservé au service vente');
        }

        $reservation = Reservation::with(['activites.activite', 'user', 'resort'])->findOrFail($numreservation);

        if ($reservation->activites->isEmpty()) {
            return back()->with('error', 'Aucune activité à annuler.');
        }

        try {
            DB::beginTransaction();

            // Préparer les données des activités pour l'email AVANT suppression
            $cancelledActivities = [];
            foreach ($reservation->activites as $resActivity) {
                $cancelledActivities[] = [
                    'nom' => $resActivity->activite->nomactivite ?? 'Activité',
                    'quantite' => $resActivity->quantite,
                    'prix_unitaire' => $resActivity->prix_unitaire,
                    'total' => $resActivity->prix_unitaire * $resActivity->quantite,
                ];
            }

            // Calculer le montant total des activités
            $totalActivities = DB::table('reservation_activite')
                ->where('numreservation', $numreservation)
                ->selectRaw('SUM(prix_unitaire * quantite) as total')
                ->value('total') ?? 0;

            // Supprimer toutes les activités
            $deletedCount = DB::table('reservation_activite')
                ->where('numreservation', $numreservation)
                ->delete();

            // Mettre à jour le prix total de la réservation
            $newTotal = $reservation->prixtotal - $totalActivities;
            $reservation->update(['prixtotal' => max(0, $newTotal)]);

            DB::commit();

            // Envoyer l'email au client
            if ($reservation->user && $reservation->user->email) {
                try {
                    Mail::to($reservation->user->email)->send(
                        new ActivityCancelledMail($reservation, $cancelledActivities, $totalActivities, false)
                    );
                    \Log::info('Email annulation toutes activités envoyé au client', [
                        'numreservation' => $numreservation,
                        'client_email' => $reservation->user->email,
                        'nb_activites' => $deletedCount,
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Erreur envoi email annulation activités: ' . $e->getMessage());
                }
            }

            \Log::info('Toutes les activités annulées par le service vente', [
                'numreservation' => $numreservation,
                'nb_activites' => $deletedCount,
                'montant_deduit' => $totalActivities,
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('vente.dashboard')->with('success', 
                "{$deletedCount} activité(s) annulée(s) avec succès. Montant total déduit : " . number_format($totalActivities, 2, ',', ' ') . " €"
            );
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur annulation toutes activités: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de l\'annulation des activités: ' . $e->getMessage());
        }
    }

    /**
     * Annule les activités en attente de validation partenaire (sans réponse)
     */
    public function cancelPendingPartnerActivities(Request $request, $numreservation)
    {
        if (!Auth::user() || (strpos(strtolower(Auth::user()->role ?? ''), 'vente') === false)) {
            abort(403, 'Accès réservé au service vente');
        }

        $reservation = Reservation::with(['user', 'resort'])->findOrFail($numreservation);

        $pendingActivities = DB::table('reservation_activite')
            ->join('activite', 'reservation_activite.numactivite', '=', 'activite.numactivite')
            ->leftJoin('partenaire', 'reservation_activite.numpartenaire', '=', 'partenaire.numpartenaire')
            ->where('reservation_activite.numreservation', $numreservation)
            ->whereNotNull('reservation_activite.numpartenaire')
            ->where('reservation_activite.partenaire_validation_status', 'pending')
            ->select(
                'reservation_activite.*',
                'activite.nomactivite',
                'partenaire.nompartenaire'
            )
            ->get();

        if ($pendingActivities->isEmpty()) {
            return back()->with('error', 'Aucune activité en attente de validation partenaire à annuler.');
        }

        try {
            DB::beginTransaction();

            $cancelledActivities = [];
            $totalRefund = 0;

            foreach ($pendingActivities as $activity) {
                $activityTotal = $activity->prix_unitaire * $activity->quantite;
                $totalRefund += $activityTotal;

                $cancelledActivities[] = [
                    'nom' => $activity->nomactivite ?? 'Activité',
                    'quantite' => $activity->quantite,
                    'prix_unitaire' => $activity->prix_unitaire,
                    'total' => $activityTotal,
                    'partenaire' => $activity->nompartenaire ?? 'Partenaire non spécifié',
                    'raison' => 'Partenaire n\'a pas répondu dans les délais',
                ];

                DB::table('reservation_activite')
                    ->where('numreservation', $numreservation)
                    ->where('numactivite', $activity->numactivite)
                    ->delete();
            }

            $newTotal = $reservation->prixtotal - $totalRefund;
            $reservation->update(['prixtotal' => max(0, $newTotal)]);

            DB::commit();

            if ($reservation->user && $reservation->user->email) {
                try {
                    Mail::to($reservation->user->email)->send(
                        new ActivityCancelledMail($reservation, $cancelledActivities, $totalRefund, false, 'partner_no_response')
                    );
                    \Log::info('Email annulation activités partenaire sans réponse envoyé', [
                        'numreservation' => $numreservation,
                        'client_email' => $reservation->user->email,
                        'nb_activites' => count($cancelledActivities),
                        'montant_rembourse' => $totalRefund,
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Erreur envoi email annulation activités partenaire: ' . $e->getMessage());
                }
            }

            \Log::info('Activités partenaire sans réponse annulées', [
                'numreservation' => $numreservation,
                'nb_activites' => count($cancelledActivities),
                'montant_rembourse' => $totalRefund,
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('vente.dashboard')->with('success', 
                count($cancelledActivities) . " activité(s) annulée(s) (partenaire sans réponse). Montant remboursé : " . number_format($totalRefund, 2, ',', ' ') . " €"
            );
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur annulation activités partenaire sans réponse: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de l\'annulation: ' . $e->getMessage());
        }
    }

    public function avisIndex()
    {
        if (!Auth::user() || (strpos(strtolower(Auth::user()->role ?? ''), 'vente') === false)) {
            abort(403, 'Acces reserve au service vente');
        }

        $avis = \App\Models\Avis::with(['user', 'resort', 'repondeur'])
            ->orderByRaw('CASE WHEN reponse IS NULL THEN 0 ELSE 1 END')
            ->orderBy('datepublication', 'desc')
            ->paginate(20);

        return view('vente.avis-index', compact('avis'));
    }

    public function avisShow($numavis)
    {
        if (!Auth::user() || (strpos(strtolower(Auth::user()->role ?? ''), 'vente') === false)) {
            abort(403, 'Acces reserve au service vente');
        }

        $avis = \App\Models\Avis::with(['user', 'resort', 'photos', 'repondeur'])
            ->findOrFail($numavis);

        return view('vente.avis-show', compact('avis'));
    }
}

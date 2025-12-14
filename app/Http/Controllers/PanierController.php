<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resort;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PanierController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        
<<<<<<< HEAD
        // Récupérer les IDs des réservations déjà payées (Confirmée ou Terminée)
        $paidReservationIds = DB::table('reservation')
            ->where('user_id', $userId)
            ->whereIn('statut', ['Confirmée', 'Terminée'])
            ->pluck('numreservation')
            ->toArray();
        
=======
        // Récupérer les réservations uniques
>>>>>>> 72585b78d1b486876d0592420f032c245d422a13
        $reservations = DB::table('reservation')
            ->join('resort', 'reservation.numresort', '=', 'resort.numresort')
            ->leftJoin('pays', 'resort.codepays', '=', 'pays.codepays')
            ->where('reservation.user_id', $userId)
            ->where('reservation.statut', 'En attente')
            ->whereNotIn('reservation.numreservation', $paidReservationIds) // Exclure les IDs déjà payés
            ->select(
                'reservation.*',
                'resort.nomresort',
                'resort.numresort',
                'pays.nompays'
            )
            ->get();

        // Pour chaque réservation, récupérer ses chambres
        foreach ($reservations as $reservation) {
            $reservation->chambres = DB::table('choisir')
                ->join('typechambre', 'choisir.numtype', '=', 'typechambre.numtype')
                ->where('choisir.numreservation', $reservation->numreservation)
                ->select('typechambre.nomtype', 'choisir.quantite')
                ->get();
        }

        return view('panier', compact('reservations'));
    }

    public function show($numreservation)
    {
        $userId = Auth::id();
        
        $reservation = DB::table('reservation')
            ->join('resort', 'reservation.numresort', '=', 'resort.numresort')
            ->leftJoin('pays', 'resort.codepays', '=', 'pays.codepays')
            ->where('reservation.numreservation', $numreservation)
            ->where('reservation.user_id', $userId)
            ->select(
                'reservation.*',
                'resort.nomresort',
                'resort.numresort',
                'pays.nompays'
            )
            ->first();

        if (!$reservation) {
            return redirect()->route('cart.index')->with('error', 'Réservation non trouvée');
        }

        $resort = Resort::with('photos')->find($reservation->numresort);
        
        // Récupérer les chambres avec quantités
        $chambres = DB::table('choisir')
            ->join('typechambre', 'choisir.numtype', '=', 'typechambre.numtype')
            ->where('choisir.numreservation', $numreservation)
            ->select('typechambre.*', 'choisir.quantite')
            ->get();

        // Calculs
        $nbNuits = Carbon::parse($reservation->datedebut)->diffInDays(Carbon::parse($reservation->datefin));
        
        // Récupérer les participants avec leurs transports
        $participants = DB::table('participant')
            ->leftJoin('transport', 'participant.numtransport', '=', 'transport.numtransport')
            ->where('participant.numreservation', $numreservation)
            ->select(
                'participant.*',
                'transport.nomtransport',
                'transport.prixtransport'
            )
            ->get();
        
        // Compter adultes et enfants
        $nbAdultes = $participants->filter(function($p) {
            return str_contains($p->nomparticipant, 'Adulte');
        })->count();
        
        $nbEnfants = $participants->filter(function($p) {
            return str_contains($p->nomparticipant, 'Enfant');
        })->count();
        
        $nbPersonnes = $nbAdultes + $nbEnfants;
        
        // Récupérer les activités avec les participants qui les ont choisies
        $activitesData = DB::table('participant_activite')
            ->join('activite', 'participant_activite.numactivite', '=', 'activite.numactivite')
            ->join('activitealacarte', 'activite.numactivite', '=', 'activitealacarte.numactivite')
            ->join('participant', 'participant_activite.numparticipant', '=', 'participant.numparticipant')
            ->where('participant.numreservation', $numreservation)
            ->select(
                'activite.numactivite',
                'activite.nomactivite',
                'activite.descriptionactivite',
                'activitealacarte.prixmin',
                'participant.nomparticipant',
                'participant.numparticipant'
            )
            ->get();
        
        // Regrouper les activités avec leurs participants
        $activites = $activitesData->groupBy('numactivite')->map(function($group) {
            $first = $group->first();
            return [
                'numactivite' => $first->numactivite,
                'nomactivite' => $first->nomactivite,
                'descriptionactivite' => $first->descriptionactivite,
                'prixmin' => $first->prixmin,
                'participants' => $group->pluck('nomparticipant')->toArray(),
                'nbParticipants' => $group->count(),
            ];
        })->values();
        
        // Calculer les prix
        $prixChambre = 0;
        foreach ($chambres as $chambre) {
            $prixParNuit = $this->getPrixChambre($chambre->numtype, $reservation->datedebut);
            $prixChambre += $prixParNuit * $nbNuits * $chambre->quantite;
        }
        
        $prixTransportTotal = $participants->sum('prixtransport');
        
        $prixActivites = 0;
        foreach ($activites as $activite) {
            $prixActivites += $activite['prixmin'] * $activite['nbParticipants'];
        }
        
        // Utiliser le prix total enregistré dans la BDD
        $total = floatval($reservation->prixtotal);
        
        // Décomposer le total pour affichage (avec TVA 20%)
        $sousTotal = $total / 1.2;
        $tva = $total - $sousTotal;

        return view('panier.detail', [
            'reservation' => $reservation,
            'resort' => $resort,
            'chambres' => $chambres,
            'participants' => $participants,
            'nbNuits' => $nbNuits,
            'nbAdultes' => $nbAdultes,
            'nbEnfants' => $nbEnfants,
            'nbPersonnes' => $nbPersonnes,
            'prixChambre' => $prixChambre,
            'prixTransportTotal' => $prixTransportTotal,
            'activites' => $activites,
            'prixActivites' => $prixActivites,
            'sousTotal' => $sousTotal,
            'tva' => $tva,
            'total' => $total,
        ]);
    }

    private function getPrixChambre($numtype, $date)
    {
        $periode = DB::table('periode')
            ->whereDate('datedebutperiode', '<=', $date)
            ->whereDate('datefinperiode', '>=', $date)
            ->first();

        if ($periode) {
            $tarif = DB::table('tarifer')
                ->where('numtype', $numtype)
                ->where('numperiode', $periode->numperiode)
                ->first();

            if ($tarif) {
                return floatval($tarif->prix);
            }
        }

        $tarif = DB::table('tarifer')
            ->where('numtype', $numtype)
            ->first();

        return $tarif ? floatval($tarif->prix) : 100;
    }

    public function remove($numreservation)
    {
        $userId = Auth::id();
        
        $reservation = DB::table('reservation')
            ->where('numreservation', $numreservation)
            ->where('user_id', $userId)
            ->first();

        if ($reservation) {
            // Récupérer les IDs des participants de cette réservation
            $participantIds = DB::table('participant')
                ->where('numreservation', $numreservation)
                ->pluck('numparticipant');
            
            // Supprimer les liens participant_activite
            if ($participantIds->isNotEmpty()) {
                DB::table('participant_activite')
                    ->whereIn('numparticipant', $participantIds)
                    ->delete();
            }
            
            // Supprimer les participants
            DB::table('participant')->where('numreservation', $numreservation)->delete();
            
            // Supprimer les activités de la réservation
            DB::table('reservation_activite')->where('numreservation', $numreservation)->delete();
            
            // Supprimer les chambres
            DB::table('choisir')->where('numreservation', $numreservation)->delete();
            
            // Supprimer la réservation
            DB::table('reservation')->where('numreservation', $numreservation)->delete();
            
            // Nettoyer aussi la session
            $reservationDetails = session('reservation_details', []);
            unset($reservationDetails[$numreservation]);
            session(['reservation_details' => $reservationDetails]);
        }

        return back()->with('success', 'Réservation supprimée.');
    }
}



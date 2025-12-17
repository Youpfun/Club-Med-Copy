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
        
        $paidReservationIds = DB::table('reservation')
            ->where('user_id', $userId)
            ->whereIn('statut', ['Confirmée', 'Terminée'])
            ->pluck('numreservation')
            ->toArray();
        
        $reservations = DB::table('reservation')
            ->join('resort', 'reservation.numresort', '=', 'resort.numresort')
            ->leftJoin('pays', 'resort.codepays', '=', 'pays.codepays')
            ->where('reservation.user_id', $userId)
            ->where('reservation.statut', 'En attente')
            ->whereNotIn('reservation.numreservation', $paidReservationIds)
            ->select(
                'reservation.*',
                'resort.nomresort',
                'resort.numresort',
                'pays.nompays'
            )
            ->get();

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
        
        $chambres = DB::table('choisir')
            ->join('typechambre', 'choisir.numtype', '=', 'typechambre.numtype')
            ->where('choisir.numreservation', $numreservation)
            ->select('typechambre.*', 'choisir.quantite')
            ->get();

        $nbNuits = Carbon::parse($reservation->datedebut)->diffInDays(Carbon::parse($reservation->datefin));
        
        $participants = DB::table('participant')
            ->leftJoin('transport', 'participant.numtransport', '=', 'transport.numtransport')
            ->where('participant.numreservation', $numreservation)
            ->select(
                'participant.*',
                'transport.nomtransport',
                'transport.prixtransport'
            )
            ->get();
        
        $nbAdultes = $participants->filter(function($p) {
            return str_contains($p->nomparticipant, 'Adulte');
        })->count();
        
        $nbEnfants = $participants->filter(function($p) {
            return str_contains($p->nomparticipant, 'Enfant');
        })->count();
        
        $nbPersonnes = $nbAdultes + $nbEnfants;
        
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
        
        $total = floatval($reservation->prixtotal);
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
            $participantIds = DB::table('participant')
                ->where('numreservation', $numreservation)
                ->pluck('numparticipant');
            
            if ($participantIds->isNotEmpty()) {
                DB::table('participant_activite')
                    ->whereIn('numparticipant', $participantIds)
                    ->delete();
            }
            
            DB::table('participant')->where('numreservation', $numreservation)->delete();
            
            DB::table('reservation_activite')->where('numreservation', $numreservation)->delete();
            
            DB::table('choisir')->where('numreservation', $numreservation)->delete();
            
            DB::table('reservation')->where('numreservation', $numreservation)->delete();
            
            $reservationDetails = session('reservation_details', []);
            unset($reservationDetails[$numreservation]);
            session(['reservation_details' => $reservationDetails]);
        }

        return back()->with('success', 'Réservation supprimée.');
    }
}



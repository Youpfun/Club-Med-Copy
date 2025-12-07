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
        
        $reservations = DB::table('reservation')
            ->join('resort', 'reservation.numresort', '=', 'resort.numresort')
            ->leftJoin('pays', 'resort.codepays', '=', 'pays.codepays')
            ->join('choisir', 'reservation.numreservation', '=', 'choisir.numreservation')
            ->join('typechambre', 'choisir.numtype', '=', 'typechambre.numtype')
            ->leftJoin('transport', 'reservation.numtransport', '=', 'transport.numtransport')
            ->where('reservation.user_id', $userId)
            ->where('reservation.statut', 'en_attente')
            ->select(
                'reservation.*',
                'resort.nomresort',
                'resort.numresort',
                'pays.nompays',
                'typechambre.nomtype',
                'typechambre.numtype',
                'transport.nomtransport',
                'transport.prixtransport'
            )
            ->get();

        return view('panier', compact('reservations'));
    }

    public function show($numreservation)
    {
        $userId = Auth::id();
        
        $reservation = DB::table('reservation')
            ->join('resort', 'reservation.numresort', '=', 'resort.numresort')
            ->leftJoin('pays', 'resort.codepays', '=', 'pays.codepays')
            ->join('choisir', 'reservation.numreservation', '=', 'choisir.numreservation')
            ->join('typechambre', 'choisir.numtype', '=', 'typechambre.numtype')
            ->leftJoin('transport', 'reservation.numtransport', '=', 'transport.numtransport')
            ->where('reservation.numreservation', $numreservation)
            ->where('reservation.user_id', $userId)
            ->select(
                'reservation.*',
                'resort.nomresort',
                'resort.numresort',
                'pays.nompays',
                'typechambre.nomtype',
                'typechambre.numtype',
                'typechambre.surface',
                'typechambre.capacitemax',
                'transport.nomtransport',
                'transport.prixtransport',
                'transport.numtransport as transport_id'
            )
            ->first();

        if (!$reservation) {
            return redirect()->route('cart.index')->with('error', 'Réservation non trouvée');
        }

        $resort = Resort::with('photos')->find($reservation->numresort);
        $typeChambre = DB::table('typechambre')->where('numtype', $reservation->numtype)->first();
        $transport = $reservation->transport_id ? DB::table('transport')->where('numtransport', $reservation->transport_id)->first() : null;

        // Calculs
        $nbNuits = Carbon::parse($reservation->datedebut)->diffInDays(Carbon::parse($reservation->datefin));
        
        // Récupérer les détails des voyageurs depuis la session
        $reservationDetails = session('reservation_details', []);
        $details = $reservationDetails[$reservation->numreservation] ?? null;
        
        if ($details) {
            $nbAdultes = $details['nbAdultes'];
            $nbEnfants = $details['nbEnfants'];
        } else {
            // Fallback si pas de données en session
            $nbAdultes = $reservation->nbpersonnes;
            $nbEnfants = 0;
        }
        $nbPersonnes = $nbAdultes + $nbEnfants;
        
        // Récupérer les activités depuis la BDD
        $activitesReservation = DB::table('reservation_activite')
            ->where('numreservation', $reservation->numreservation)
            ->get();
        
        $activites = [];
        $prixActivites = 0;
        
        if ($activitesReservation->count() > 0) {
            // Récupérer les détails des activités
            $activitesIds = $activitesReservation->pluck('numactivite')->toArray();
            
            $activites = DB::table('activite')
                ->join('activitealacarte', 'activite.numactivite', '=', 'activitealacarte.numactivite')
                ->whereIn('activite.numactivite', $activitesIds)
                ->select('activite.numactivite', 'activite.nomactivite', 'activitealacarte.prixmin')
                ->get();
            
            // Calculer le prix total des activités
            foreach ($activitesReservation as $actRes) {
                $prixActivites += $actRes->prix_unitaire * $actRes->quantite;
            }
        }
        
        // Prix chambre - calculer le nombre de chambres nécessaires pour l'affichage
        $capaciteMax = $typeChambre->capacitemax ?? 2;
        $nbChambres = ceil($nbPersonnes / $capaciteMax);
        $prixParNuit = $this->getPrixChambre($reservation->numtype, $reservation->datedebut);
        $prixChambre = $prixParNuit * $nbNuits * $nbChambres;
        
        // Prix transport
        $prixTransportParPersonne = $transport ? floatval($transport->prixtransport) : 0;
        $prixTransportAdultes = $prixTransportParPersonne * $nbAdultes;
        $prixTransportEnfants = $prixTransportParPersonne * $nbEnfants;
        $prixTransportTotal = $prixTransportAdultes + $prixTransportEnfants;
        
        // Utiliser le prix total enregistré dans la BDD (fixé au moment de la réservation)
        $total = floatval($reservation->prixtotal);
        
        // Décomposer le total pour affichage (avec TVA 20%)
        $sousTotal = $total / 1.2;
        $tva = $total - $sousTotal;

        return view('panier.detail', [
            'reservation' => $reservation,
            'resort' => $resort,
            'typeChambre' => $typeChambre,
            'transport' => $transport,
            'nbNuits' => $nbNuits,
            'nbAdultes' => $nbAdultes,
            'nbEnfants' => $nbEnfants,
            'nbPersonnes' => $nbPersonnes,
            'nbChambres' => $nbChambres,
            'prixParNuit' => $prixParNuit,
            'prixChambre' => $prixChambre,
            'prixTransportParPersonne' => $prixTransportParPersonne,
            'prixTransportAdultes' => $prixTransportAdultes,
            'prixTransportEnfants' => $prixTransportEnfants,
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
            DB::table('reservation_activite')->where('numreservation', $numreservation)->delete();
            DB::table('choisir')->where('numreservation', $numreservation)->delete();
            DB::table('reservation')->where('numreservation', $numreservation)->delete();
            
            // Nettoyer aussi la session
            $reservationDetails = session('reservation_details', []);
            unset($reservationDetails[$numreservation]);
            session(['reservation_details' => $reservationDetails]);
        }

        return back()->with('success', 'Réservation supprimée.');
    }
}



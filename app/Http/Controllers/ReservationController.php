<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Resort;
use App\Models\Typechambre;
use App\Models\Transport;
use App\Models\Activite;

class ReservationController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $today = Carbon::today();

        $reservations = DB::table('reservation')
            ->join('resort', 'reservation.numresort', '=', 'resort.numresort') 
            ->join('pays', 'resort.codepays', '=', 'pays.codepays')
            ->join('choisir', 'reservation.numreservation', '=', 'choisir.numreservation')
            ->join('typechambre', 'choisir.numtype', '=', 'typechambre.numtype')
            ->where('reservation.user_id', $userId)
            ->whereIn('reservation.statut', ['payee', 'confirmee'])
            ->select(
                'reservation.*', 
                'resort.nomresort', 
                'resort.numresort',
                'pays.nompays',
                'typechambre.nomtype as type_chambre'
            )
            ->orderBy('reservation.datedebut', 'desc')
            ->get();

        // Récupérer les réservations en attente (panier)
        $panierResorts = DB::table('reservation')
            ->join('resort', 'reservation.numresort', '=', 'resort.numresort') 
            ->join('pays', 'resort.codepays', '=', 'pays.codepays')
            ->join('choisir', 'reservation.numreservation', '=', 'choisir.numreservation')
            ->join('typechambre', 'choisir.numtype', '=', 'typechambre.numtype')
            ->where('reservation.user_id', $userId)
            ->where('reservation.statut', 'en_attente')
            ->select(
                'reservation.*', 
                'resort.nomresort', 
                'resort.numresort',
                'pays.nompays',
                'typechambre.nomtype as type_chambre'
            )
            ->orderBy('reservation.datedebut', 'asc')
            ->get();

        $enCours = $reservations->filter(function ($res) use ($today) {
            return $today->between($res->datedebut, $res->datefin);
        });

        $aVenir = $reservations->filter(function ($res) use ($today) {
            return $res->datedebut > $today;
        });

        $terminees = $reservations->filter(function ($res) use ($today) {
            return $res->datefin < $today;
        });

        return view('reservations', [
            'enCours' => $enCours,
            'aVenir' => $aVenir,
            'terminees' => $terminees,
            'panierResorts' => $panierResorts
        ]);
    }

    public function step1($numresort)
    {
        $resort = Resort::with(['photos', 'pays'])->findOrFail($numresort);
        $typeChambres = DB::table('typechambre')
            ->join('proposer', 'typechambre.numtype', '=', 'proposer.numtype')
            ->where('proposer.numresort', $numresort)
            ->select('typechambre.*')
            ->get();

        return view('reservation.step1', compact('resort', 'typeChambres'));
    }

    public function step2(Request $request, $numresort)
    {
        $resort = Resort::with(['photos', 'pays'])->findOrFail($numresort);
        $transports = Transport::all();
        
        $dateDebut = $request->query('dateDebut');
        $dateFin = $request->query('dateFin');
        $numtype = $request->query('numtype');
        $numtransport = $request->query('numtransport');
        $nbAdultes = $request->query('nbAdultes', 1);
        $nbEnfants = $request->query('nbEnfants', 0);

        $typeChambre = Typechambre::find($numtype);

        return view('reservation.step2', compact('resort', 'transports', 'dateDebut', 'dateFin', 'numtype', 'numtransport', 'nbAdultes', 'nbEnfants', 'typeChambre'));
    }

    public function step3(Request $request, $numresort)
    {
        $resort = Resort::with(['photos', 'pays'])->findOrFail($numresort);
        
        $dateDebut = $request->query('dateDebut');
        $dateFin = $request->query('dateFin');
        $numtype = $request->query('numtype');
        $numtransport = $request->query('numtransport');
        $nbAdultes = $request->query('nbAdultes', 1);
        $nbEnfants = $request->query('nbEnfants', 0);

        $typeChambre = Typechambre::find($numtype);
        $transport = $numtransport ? Transport::find($numtransport) : null;

        // Récupérer les activités à la carte du resort
        try {
            $activites = DB::table('activite')
                ->join('activitealacarte', 'activite.numactivite', '=', 'activitealacarte.numactivite')
                ->join('typeactivite', 'activite.numtypeactivite', '=', 'typeactivite.numtypeactivite')
                ->join('partager', 'typeactivite.numtypeactivite', '=', 'partager.numtypeactivite')
                ->where('partager.numresort', $numresort)
                ->select('activite.numactivite', 'activite.nomactivite', 'activite.descriptionactivite', 'activitealacarte.prixmin')
                ->distinct()
                ->get();
        } catch (\Exception $e) {
            $activites = collect([]);
        }

        $nbNuits = Carbon::parse($dateDebut)->diffInDays(Carbon::parse($dateFin));
        
        $prixParNuit = $this->getPrixChambre($numtype, $dateDebut);
        $prixChambre = $prixParNuit * $nbNuits;
        $prixTransport = $transport ? $transport->prixtransport * ($nbAdultes + $nbEnfants) : 0;

        return view('reservation.step3', compact(
            'resort', 'activites', 'dateDebut', 'dateFin', 'numtype', 'numtransport',
            'nbAdultes', 'nbEnfants', 'typeChambre', 'transport', 'nbNuits', 'prixChambre', 'prixTransport'
        ));
    }

    public function addToCart(Request $request, $numresort)
    {
        $request->validate([
            'dateDebut' => 'required|date',
            'dateFin' => 'required|date|after:dateDebut',
            'numtype' => 'required|integer',
            'nbAdultes' => 'required|integer|min:1',
            'nbEnfants' => 'required|integer|min:0',
        ]);

        $dateDebut = $request->input('dateDebut');
        $dateFin = $request->input('dateFin');
        $numtype = $request->input('numtype');
        $numtransport = $request->input('numtransport');
        $nbAdultes = $request->input('nbAdultes');
        $nbEnfants = $request->input('nbEnfants');
        $activites = $request->input('activites', []);

        $nbNuits = Carbon::parse($dateDebut)->diffInDays(Carbon::parse($dateFin));
        $nbPersonnes = $nbAdultes + $nbEnfants;

        $prixParNuit = $this->getPrixChambre($numtype, $dateDebut);
        $prixChambre = $prixParNuit * $nbNuits;

        $transport = $numtransport ? Transport::find($numtransport) : null;
        $prixTransport = $transport ? $transport->prixtransport * $nbPersonnes : 0;

        $prixActivites = 0;
        foreach ($activites as $numactivite) {
            $activite = DB::table('activitealacarte')->where('numactivite', $numactivite)->first();
            if ($activite) {
                $prixActivites += $activite->prixmin * $nbPersonnes;
            }
        }

        $sousTotal = $prixChambre + $prixTransport + $prixActivites;
        $tva = $sousTotal * 0.2;
        $prixTotal = $sousTotal + $tva;

        $numreservation = DB::table('reservation')->insertGetId([
            'user_id' => Auth::id(),
            'numresort' => $numresort,
            'numjour' => 1,
            'pla_numjour' => 1,
            'numtransport' => $numtransport,
            'statut' => 'en_attente',
            'nbpersonnes' => $nbPersonnes,
            'prixtotal' => $prixTotal,
            'datedebut' => $dateDebut,
            'datefin' => $dateFin,
        ], 'numreservation');

        DB::table('choisir')->insert([
            'numreservation' => $numreservation,
            'numtype' => $numtype,
        ]);

        // Stocker les détails des voyageurs en session
        $reservationDetails = session('reservation_details', []);
        $reservationDetails[$numreservation] = [
            'nbAdultes' => $nbAdultes,
            'nbEnfants' => $nbEnfants,
        ];
        session(['reservation_details' => $reservationDetails]);

        return redirect()->route('cart.index')->with('success', 'Réservation ajoutée au panier !');
    }

    public function getPrix(Request $request)
    {
        $numtype = $request->input('numtype');
        $dateDebut = $request->input('dateDebut');
        $dateFin = $request->input('dateFin');

        if (!$numtype) {
            return response()->json(['error' => 'Paramètres manquants'], 400);
        }

        // Si pas de dates, retourner juste le prix par nuit (prix par défaut)
        if (!$dateDebut || !$dateFin) {
            $prixParNuit = $this->getPrixChambre($numtype, now()->format('Y-m-d'));
            return response()->json([
                'prixParNuit' => $prixParNuit
            ]);
        }

        $nbNuits = Carbon::parse($dateDebut)->diffInDays(Carbon::parse($dateFin));
        $prixParNuit = $this->getPrixChambre($numtype, $dateDebut);
        $prixTotal = $prixParNuit * $nbNuits;

        return response()->json([
            'prixParNuit' => $prixParNuit,
            'nbNuits' => $nbNuits,
            'prixTotal' => $prixTotal
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
                return $tarif->prix;
            }
        }

        $tarif = DB::table('tarifer')
            ->where('numtype', $numtype)
            ->first();

        return $tarif ? $tarif->prix : 100;
    }
}
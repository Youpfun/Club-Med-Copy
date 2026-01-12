<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Resort;
use App\Models\Indisponibilite;
use App\Models\Chambre;
use App\Models\Reservation;

class IndisponibiliteController extends Controller
{
    public function index()
    {
        $indisponibilites = Indisponibilite::with(['chambre.typechambre', 'chambre.typechambre.resorts'])
            ->where('datefin', '>=', now()) 
            ->orderBy('datedebut')
            ->get();

        return view('marketing.indisponibilite.index', compact('indisponibilites'));
    }

    public function occupancy(Request $request)
    {
        $resorts = Resort::orderBy('nomresort')->get();
        $numresort = $request->input('numresort', $resorts->first()->numresort ?? 0);
        $selectedResort = $resorts->where('numresort', $numresort)->first();

        $dateDebut = $request->input('date_debut', now()->format('Y-m-d'));
        $dateFin = $request->input('date_fin', now()->addMonth()->format('Y-m-d'));

        $stats = [];
        $reservations = collect();
        $roomsView = collect(); // Nouvelle collection pour l'affichage mappé

        if ($selectedResort) {
            $types = $selectedResort->typechambres;
            
            foreach ($types as $type) {
                // 1. Récupérer toutes les chambres physiques de ce type
                $chambresPhysiques = Chambre::where('numtype', $type->numtype)
                    ->with(['indisponibilites' => function($q) use ($dateDebut, $dateFin) {
                        $q->where('datedebut', '<', $dateFin)
                          ->where('datefin', '>', $dateDebut);
                    }])
                    ->orderBy('numchambre')
                    ->get();

                // 2. Récupérer toutes les réservations actives pour ce type sur la période
                $reservationsType = Reservation::query()
                    ->join('choisir', 'reservation.numreservation', '=', 'choisir.numreservation')
                    ->where('reservation.numresort', $numresort)
                    ->where('choisir.numtype', $type->numtype)
                    ->whereIn('reservation.statut', ['Confirmée', 'Validée', 'En attente'])
                    ->where(function($q) use ($dateDebut, $dateFin) {
                        $q->where('reservation.datedebut', '<', $dateFin)
                          ->where('reservation.datefin', '>', $dateDebut);
                    })
                    ->select('reservation.*', 'choisir.quantite')
                    ->with('user')
                    ->get();

                // 3. STATS (Calculs existants)
                $totalPhysique = $chambresPhysiques->count();
                $nbBloquees = $chambresPhysiques->filter(fn($c) => $c->indisponibilites->isNotEmpty())->count();
                $nbReservees = $reservationsType->sum('quantite');

                $stats[] = [
                    'type' => $type,
                    'total' => $totalPhysique,
                    'bloquees' => $nbBloquees,
                    'reservees' => $nbReservees,
                    'dispo' => $totalPhysique - $nbBloquees - $nbReservees
                ];

                // 4. MAPPING VIRTUEL (Distribution des clients dans les chambres libres)
                // On crée une file d'attente de clients à placer
                $clientsQueue = collect();
                foreach($reservationsType as $res) {
                    for($i=0; $i < $res->quantite; $i++) {
                        $clientsQueue->push($res);
                    }
                }

                // On parcourt chaque chambre physique pour lui assigner un état
                foreach($chambresPhysiques as $chambre) {
                    $etat = 'libre';
                    $reservationAssignee = null;
                    $indispo = null;

                    // A. Est-elle bloquée ?
                    if ($chambre->indisponibilites->isNotEmpty()) {
                        $etat = 'bloquee';
                        $indispo = $chambre->indisponibilites->first();
                    } 
                    // B. Sinon, met-on un client dedans ?
                    elseif ($clientsQueue->isNotEmpty()) {
                        $etat = 'occupee';
                        $reservationAssignee = $clientsQueue->shift(); // On prend le premier client et on le retire de la file
                    }

                    $roomsView->push([
                        'chambre' => $chambre,
                        'type' => $type,
                        'etat' => $etat,
                        'indispo' => $indispo,
                        'reservation' => $reservationAssignee
                    ]);
                }
            }
        }

        return view('marketing.indisponibilite.occupancy', compact(
            'resorts', 'selectedResort', 'stats', 'roomsView', 'dateDebut', 'dateFin'
        ));
    }

    public function destroy($id)
    {
        $indispo = Indisponibilite::findOrFail($id);
        $indispo->delete();
        return back()->with('success', 'La chambre est de nouveau disponible à la vente.');
    }

    public function selectResort()
    {
        $resorts = Resort::orderBy('nomresort')->get();
        return view('marketing.indisponibilite.select', compact('resorts'));
    }

    public function create($numresort)
    {
        $resort = Resort::findOrFail($numresort);
        $types = $resort->typechambres()->with(['chambres' => function($q) {
            $q->orderBy('numchambre');
        }])->get();

        return view('marketing.indisponibilite.create', compact('resort', 'types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'idchambre' => 'required|exists:chambre,idchambre',
            'datedebut' => 'required|date|after_or_equal:today',
            'datefin' => 'required|date|after:datedebut',
            'motif' => 'required|string|max:255',
        ]);

        $exists = Indisponibilite::where('idchambre', $request->idchambre)
            ->where(function($q) use ($request) {
                $q->whereBetween('datedebut', [$request->datedebut, $request->datefin])
                  ->orWhereBetween('datefin', [$request->datedebut, $request->datefin]);
            })
            ->exists();

        if ($exists) {
            return back()->withErrors(['error' => 'Cette chambre est déjà déclarée indisponible sur cette période.']);
        }

        if (!$request->has('force')) {
            $conflits = $this->detecterConflits(
                $request->idchambre, 
                $request->datedebut, 
                $request->datefin
            );

            if ($conflits['has_conflict']) {
                // Si l'utilisateur voulait spécifiquement "bloquer pour déplacer QQUN",
                // on peut passer l'ID de la réservation cible pour l'UI de conflit
                return view('marketing.indisponibilite.conflict', [
                    'conflits' => $conflits,
                    'input' => $request->all(),
                    'target_reservation' => $request->input('target_reservation') // Nouveau champ optionnel
                ]);
            }
        }

        Indisponibilite::create($request->except(['force', 'target_reservation']));

        $message = 'Indisponibilité enregistrée.';
        if ($request->has('force')) {
            $message .= ' Attention : Un surbooking a potentiellement été créé.';
        }

        return redirect()->route('marketing.indisponibilite.index')
                         ->with('success', $message);
    }

    private function detecterConflits($idchambre, $debut, $fin)
    {
        $chambre = Chambre::findOrFail($idchambre);
        $numtype = $chambre->numtype;

        $resortLink = DB::table('proposer')->where('numtype', $numtype)->first();
        if (!$resortLink) return ['has_conflict' => false];
        $numresort = $resortLink->numresort;

        $totalChambres = Chambre::where('numtype', $numtype)->count();

        $chambresBloquees = Indisponibilite::whereHas('chambre', function($q) use ($numtype) {
                $q->where('numtype', $numtype);
            })
            ->where(function($q) use ($debut, $fin) {
                $q->where('datedebut', '<', $fin)
                  ->where('datefin', '>', $debut);
            })
            ->count();

        $capaciteDispo = $totalChambres - $chambresBloquees;

        $reservationsImpactees = Reservation::query()
            ->join('choisir', 'reservation.numreservation', '=', 'choisir.numreservation')
            ->where('reservation.numresort', $numresort)
            ->where('choisir.numtype', $numtype)
            ->whereIn('reservation.statut', ['Confirmée', 'Validée', 'Terminée', 'En attente'])
            ->where(function($q) use ($debut, $fin) {
                $q->where('reservation.datedebut', '<', $fin)
                  ->where('reservation.datefin', '>', $debut);
            })
            ->select('reservation.*', 'choisir.quantite')
            ->with('user')
            ->get();

        $totalReserve = $reservationsImpactees->sum('quantite');
        
        if ($totalReserve > ($capaciteDispo - 1)) {
            return [
                'has_conflict' => true,
                'total_chambres' => $totalChambres,
                'chambres_bloquees' => $chambresBloquees,
                'chambres_bloquees_apres' => $chambresBloquees + 1,
                'total_reserve' => $totalReserve,
                'reservations' => $reservationsImpactees
            ];
        }

        return ['has_conflict' => false];
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resort;
use App\Models\Indisponibilite;

class IndisponibiliteController extends Controller
{
    // --- AJOUT : LISTER LES INDISPONIBILITÉS ---
    public function index()
    {
        // On récupère les indisponibilités futures ou en cours
        // On charge la relation 'chambre' et 'typechambre' pour afficher les infos
        $indisponibilites = Indisponibilite::with(['chambre.typechambre'])
            ->where('datefin', '>=', now()) // On ne montre pas le passé
            ->orderBy('datedebut')
            ->get();

        return view('marketing.indisponibilite.index', compact('indisponibilites'));
    }

    // --- AJOUT : SUPPRIMER (LIBÉRER LA CHAMBRE) ---
    public function destroy($id)
    {
        $indispo = Indisponibilite::findOrFail($id);
        $indispo->delete();

        return back()->with('success', 'La chambre est de nouveau disponible à la vente.');
    }

    // ... (Vos méthodes selectResort, create et store restent inchangées) ...
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
            return back()->withErrors(['error' => 'Cette chambre est déjà indisponible sur cette période.']);
        }

        Indisponibilite::create($request->all());

        // On redirige vers la liste plutôt que le dashboard pour voir le résultat
        return redirect()->route('marketing.indisponibilite.index')
                         ->with('success', 'Indisponibilité enregistrée.');
    }
}
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Resort;
use App\Models\TypeActivite;
use App\Models\Partenaire;
use Illuminate\Support\Facades\DB;

class ActiviteController extends Controller
{
    /**
     * Affiche les TYPES d'activités disponibles pour ce resort
     */
    public function indexTypes($id)
    {
        // On récupère le resort
        $resort = Resort::findOrFail($id);

        // On cherche le partenaire du resort (c'est lui qui détient la liste des activités)
        $partnerName = 'Service ' . $resort->nomresort;
        $partner = Partenaire::where('nompartenaire', $partnerName)->first();

        $types = collect();

        if ($partner) {
            // On récupère uniquement les types qui ont au moins une activité liée à ce partenaire
            $types = TypeActivite::whereHas('activites', function ($query) use ($partner) {
                $query->join('fourni', 'activite.numactivite', '=', 'fourni.numactivite')
                      ->where('fourni.numpartenaire', $partner->numpartenaire);
            })->get();
        }

        return view('types_activites', [
            'resort' => $resort,
            'types' => $types
        ]);
    }

    /**
     * Affiche les ACTIVITÉS d'un type précis pour ce resort (Filtré !)
     */
    public function indexActivitesParType($id, $typeId)
    {
        $resort = Resort::findOrFail($id);
        $typeActivite = TypeActivite::findOrFail($typeId);

        // On récupère le partenaire
        $partnerName = 'Service ' . $resort->nomresort;
        $partner = Partenaire::where('nompartenaire', $partnerName)->first();

        // Si pas de partenaire, pas d'activités
        if (!$partner) {
            return view('activites_detail', [
                'resort' => $resort,
                'typeActivite' => $typeActivite,
                'activites' => collect()
            ]);
        }

        // REQUÊTE CORRIGÉE : On filtre par le partenaire du resort
        $activites = DB::table('activite')
            ->join('fourni', 'activite.numactivite', '=', 'fourni.numactivite')
            ->where('fourni.numpartenaire', $partner->numpartenaire) // FILTRE CRUCIAL
            ->where('activite.numtypeactivite', $typeId)             // Filtre par type
            ->select(
                'activite.*', 
                'fourni.est_incluse as est_incluse_resort', // On prend l'info spécifique
                'fourni.prix as prix_resort'                // On prend le prix spécifique
            )
            ->get();

        return view('activites_detail', [
            'resort' => $resort,
            'typeActivite' => $typeActivite,
            'activites' => $activites
        ]);
    }
    
    // Si tu as besoin d'une méthode index globale (toutes activités)
    public function index($id) {
        // ... (Logique similaire si nécessaire)
    }
}
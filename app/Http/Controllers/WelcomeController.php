<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resort;
use App\Models\Localisation;
use Illuminate\Support\Facades\DB;

class WelcomeController extends Controller
{
    public function index()
    {
        // Récupérer 4 resorts populaires pour les afficher sur la page d'accueil
        // On cherche des resorts avec des images connues ou les mieux notés
        $featuredResorts = Resort::with(['pays', 'avis', 'photos'])
            ->withCount('avis')
            ->orderByDesc('avis_count')
            ->take(8)
            ->get();

        // Récupérer les localisations pour les filtres
        $localisations = DB::table('localisation')->pluck('nomlocalisation', 'numlocalisation');
        
        // Pour Ski/Montagne : 
        // - Localisation "Les Alpes" (numlocalisation = 1)
        // - TypeClub "Montagne" (numtypeclub = 1)
        // - Regroupement "Premières Neiges" (numregroupement = 8)
        $skiLocalisation = DB::table('localisation')
            ->where('nomlocalisation', 'LIKE', '%Alpes%')
            ->first();
            
        $skiTypeclub = DB::table('typeclub')
            ->where('nomtypeclub', 'LIKE', '%Montagne%')
            ->first();
            
        // Pour Soleil/Plage :
        // - TypeClub "Mer & Plage" (numtypeclub = 2)
        // - Regroupement "Soleil d'Hiver" ou "Bureau au Soleil"
        $soleilTypeclub = DB::table('typeclub')
            ->where('nomtypeclub', 'LIKE', '%Mer%')
            ->orWhere('nomtypeclub', 'LIKE', '%Plage%')
            ->first();
            
        $soleilRegroupement = DB::table('regroupementclub')
            ->where('nomregroupement', 'LIKE', '%Soleil%')
            ->first();

        return view('welcome', compact(
            'featuredResorts',
            'localisations',
            'skiLocalisation',
            'skiTypeclub',
            'soleilTypeclub',
            'soleilRegroupement'
        ));
    }
}

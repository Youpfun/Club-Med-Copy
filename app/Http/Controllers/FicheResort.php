<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resort;

class FicheResort extends Controller
{
    public function fiche(Request $request, $numresort)
    {
        $resort = Resort::with([
            'pays', 
            'domaineskiable', 
            'typechambres', 
            'restaurants',
            'regroupements',
            'localisations',
            'avis' => function($query) {
                $query->orderBy('datepublication', 'desc');
            },
            'avis.user',
            'avis.photos'
        ])
        ->where('numresort', $numresort)
        ->firstOrFail();

        // Récupérer les resorts similaires (même pays, mêmes regroupements ou localisations)
        $regroupementIds = $resort->regroupements->pluck('numregroupement')->toArray();
        $localisationIds = $resort->localisations->pluck('numlocalisation')->toArray();

        $similarResorts = Resort::with(['pays', 'photos', 'regroupements'])
            ->where('numresort', '!=', $numresort)
            ->where(function($query) use ($resort, $numresort, $regroupementIds, $localisationIds) {
                // Même pays
                $query->where('codepays', $resort->codepays);
                
                // Ou mêmes regroupements
                if (!empty($regroupementIds)) {
                    $query->orWhere(function($q) use ($numresort, $regroupementIds) {
                        $q->where('numresort', '!=', $numresort)
                          ->whereHas('regroupements', function($subq) use ($regroupementIds) {
                              $subq->whereIn('appartenir.numregroupement', $regroupementIds);
                          });
                    });
                }
                
                // Ou mêmes localisations
                if (!empty($localisationIds)) {
                    $query->orWhere(function($q) use ($numresort, $localisationIds) {
                        $q->where('numresort', '!=', $numresort)
                          ->whereHas('localisations', function($subq) use ($localisationIds) {
                              $subq->whereIn('situer2.numlocalisation', $localisationIds);
                          });
                    });
                }
            })
            ->inRandomOrder()
            ->limit(6)
            ->get();

        // Récupérer les resorts récemment consultés depuis la session
        $recentlyViewedIds = $request->session()->get('recently_viewed_resorts', []);
        
        // Récupérer les resorts récemment consultés (hors le resort actuel)
        $recentlyViewedResorts = collect();
        if (!empty($recentlyViewedIds)) {
            $filteredIds = array_filter($recentlyViewedIds, fn($id) => $id != $numresort);
            if (!empty($filteredIds)) {
                $recentlyViewedResorts = Resort::with(['pays', 'photos', 'regroupements'])
                    ->whereIn('numresort', $filteredIds)
                    ->get()
                    ->sortBy(function($resort) use ($filteredIds) {
                        return array_search($resort->numresort, $filteredIds);
                    });
            }
        }

        // Ajouter le resort actuel à l'historique (au début de la liste)
        $recentlyViewedIds = array_filter($recentlyViewedIds, fn($id) => $id != $numresort);
        array_unshift($recentlyViewedIds, (int)$numresort);
        // Garder seulement les 10 derniers resorts consultés
        $recentlyViewedIds = array_slice($recentlyViewedIds, 0, 10);
        $request->session()->put('recently_viewed_resorts', $recentlyViewedIds);

        return view('ficheresort', [
            'resort' => $resort,
            'similarResorts' => $similarResorts,
            'recentlyViewedResorts' => $recentlyViewedResorts
        ]);
    }
}
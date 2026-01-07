<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resort;

class ResortSearchController extends Controller
{
    /**
     * Recherche de resorts pour la barre de recherche dynamique
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }
        
        $resorts = Resort::with(['pays', 'photos'])
            ->where(function($q) use ($query) {
                $q->where('nomresort', 'ILIKE', '%' . $query . '%')
                  ->orWhereHas('pays', function($paysQuery) use ($query) {
                      $paysQuery->where('nompays', 'ILIKE', '%' . $query . '%');
                  });
            })
            ->limit(8)
            ->get()
            ->map(function($resort) {
                $photo = $resort->photos->first();
                return [
                    'numresort' => $resort->numresort,
                    'nomresort' => $resort->nomresort,
                    'pays' => $resort->pays->nompays ?? 'Destination Club Med',
                    'photo' => $photo ? asset('img/ressort/' . $photo->urlphoto) : null,
                    'nbtridents' => $resort->nbtridents,
                    'moyenneavis' => $resort->moyenneavis,
                ];
            });
        
        return response()->json($resorts);
    }
}

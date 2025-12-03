<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resort;

class FicheResort extends Controller
{
    public function fiche($numresort)
    {
        $resort = Resort::with([
            'pays', 
            'domaineskiable', 
            'typechambres', 
            'avis' => function($query) {
                $query->orderBy('datepublication', 'desc');
            },
            'avis.user',
            'avis.photos'
        ])
        ->where('numresort', $numresort)
        ->firstOrFail();

        return view('ficheresort', ['resort' => $resort]);
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resort;

class FicheResort extends Controller
{
    public function fiche($numresort)
    {
        $resort = Resort::with(['pays', 'avis' => function($query) {
            $query->orderBy('datepublication', 'desc')->take(3);
        }, 'typechambres', 'domaineskiable'])->find($numresort);

        return view('ficheresort', ['resort' => $resort]);
    }
}

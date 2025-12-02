<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Activite;
use App\Models\Resort;

class ActiviteController extends Controller
{
    public function index($id)
    {
        $resort = Resort::findOrFail($id);

        $activites = Activite::select('activite.*')
            ->join('typeactivite', 'activite.numtypeactivite', '=', 'typeactivite.numtypeactivite')
            ->join('partager', 'typeactivite.numtypeactivite', '=', 'partager.numtypeactivite')
            ->where('partager.numresort', $id)
            ->get();

        return view('activites', [
            'activites' => $activites,
            'resort' => $resort 
        ]);
    }
}
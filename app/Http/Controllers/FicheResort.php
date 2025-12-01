<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resort;

class FicheResort extends Controller
{
    public function fiche($numresort)
    {
        $resort = Resort::resortPaysAvis($numresort);

        return view('ficheresort', ['resort' => $resort]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resort;

class FicheResort extends Controller
{
    public function fiche($numresort)
    {
        $resort = Resort::resortPaysDocumentationAvis($numresort);

        return view('ficheresort', ['resort' => $resort]);
    }
}

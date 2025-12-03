<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Resort;
use App\Models\TypeActivite;

class ActiviteController extends Controller
{
    public function indexTypes($id)
    {
        $resort = Resort::with('typesActivites')->findOrFail($id);

        return view('types_activites', [
            'resort' => $resort,
            'types' => $resort->typesActivites
        ]);
    }

    public function indexActivitesParType($id, $typeId)
    {
        $resort = Resort::findOrFail($id);
        
        $typeActivite = TypeActivite::with('activites')->findOrFail($typeId);

        return view('activites_detail', [
            'resort' => $resort,
            'typeActivite' => $typeActivite,
            'activites' => $typeActivite->activites
        ]);
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Localisation;

class LocalisationController extends Controller
{
    public function index()
	{
		$localisations  = Localisation::all();

		return view('localisations', ['localisations' => $localisations]);
	}
}

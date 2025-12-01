<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resort;
use Illuminate\Support\Facades\DB;

class ResortController extends Controller
{
	public function index(Request $request)
	{
		$typeclub = $request->input('typeclub');
		$localisation = $request->input('localisation');
		$pays = $request->input('pays');

		// Types de clubs pour le menu déroulant
		$typeclubs = DB::table('typeclub')->pluck('nomtypeclub', 'numtypeclub');
		
		// Localisations pour le menu déroulant
		$localisations = DB::table('localisation')->pluck('nomlocalisation', 'numlocalisation');
		
		// Pays pour le menu déroulant
		$paysList = DB::table('pays')->pluck('nompays', 'codepays');

		// Resorts avec filtres optionnels
		$resorts = Resort::when($typeclub, function($query, $typeclub) {
			return $query->whereHas('typeclubs', function($q) use ($typeclub) {
				$q->where('typeclub.numtypeclub', $typeclub);
			});
		})
		->when($localisation, function($query, $localisation) {
			return $query->whereHas('localisations', function($q) use ($localisation) {
				$q->where('localisation.numlocalisation', $localisation);
			});
		})
		->when($pays, function($query, $pays) {
			return $query->where('resort.codepays', $pays);
		})
		->orderBy('nomresort')->get();

		return view('resorts', compact('resorts', 'typeclubs', 'localisations', 'paysList'));
	}
}

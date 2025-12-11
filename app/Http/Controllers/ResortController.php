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

		$typeclubs = DB::table('typeclub')->pluck('nomtypeclub', 'numtypeclub');
		
		$localisations = DB::table('localisation')->pluck('nomlocalisation', 'numlocalisation');
		
		$paysList = DB::table('pays')->pluck('nompays', 'codepays');

		// Chargement optimisé : eager loading des avis (notes uniquement) et pagination
		$resorts = Resort::with(['avis' => function($query) {
				$query->select('numavis', 'numresort', 'noteavis');
			}])
			->when($typeclub, function($query, $typeclub) {
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
			->orderBy('nomresort')
			->paginate(12)
			->appends($request->query());

		return view('resorts', compact('resorts', 'typeclubs', 'localisations', 'paysList'));
	}
}

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

		// Types de clubs pour le menu déroulant
		$typeclubs = DB::table('typeclub')->pluck('nomtypeclub', 'numtypeclub');

		// Resorts avec filtre optionnel
		$resorts = Resort::when($typeclub, function($query, $typeclub) {
			return $query->whereHas('typeclubs', function($q) use ($typeclub) {
				$q->where('typeclub.numtypeclub', $typeclub);
			});
		})->orderBy('nomresort')->get();

		return view('resorts', compact('resorts', 'typeclubs'));
	}
}

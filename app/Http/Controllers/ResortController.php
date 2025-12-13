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
        $activite = $request->input('activite');
        $regroupement = $request->input('regroupement');
        $tri = $request->input('tri');

        $typeclubs = DB::table('typeclub')->pluck('nomtypeclub', 'numtypeclub');
        $localisations = DB::table('localisation')->pluck('nomlocalisation', 'numlocalisation');
        $paysList = DB::table('pays')->orderBy('nompays')->pluck('nompays', 'codepays');
        $activitesList = DB::table('typeactivite')->pluck('nomtypeactivite', 'numtypeactivite');
        $regroupementsList = DB::table('regroupementclub')->pluck('nomregroupement', 'numregroupement');

        $query = Resort::query();

        $query->select('resort.*');

        $priceSubquery = DB::table('tarifer')
            ->join('proposer', 'tarifer.numtype', '=', 'proposer.numtype')
            ->selectRaw('CAST(MIN(tarifer.prix) + (resort.numresort * 0.2) + (resort.nbtridents * 5) AS DECIMAL(10,0))')
            ->whereColumn('proposer.numresort', 'resort.numresort');

        $query->selectSub($priceSubquery, 'min_price');

        $query->with(['avis' => function($q) {
            $q->select('numavis', 'numresort', 'noteavis');
        }]);

        $query->when($typeclub, function($q, $val) { 
            return $q->whereHas('typeclubs', function($sub) use ($val) { 
                $sub->where('typeclub.numtypeclub', $val); 
            }); 
        });

        $query->when($localisation, function($q, $val) { 
            return $q->whereHas('localisations', function($sub) use ($val) { 
                $sub->where('localisation.numlocalisation', $val); 
            }); 
        });

        $query->when($pays, function($q, $val) { 
            return $q->where('resort.codepays', $val); 
        });

        $query->when($activite, function($q, $val) { 
            return $q->whereHas('typesActivites', function($sub) use ($val) { 
                $sub->where('typeactivite.numtypeactivite', $val); 
            }); 
        });

        $query->when($regroupement, function($q, $val) { 
            return $q->whereHas('regroupements', function($sub) use ($val) { 
                $sub->where('regroupementclub.numregroupement', $val); 
            }); 
        });

        if ($tri === 'prix_asc') {
            $query->orderByRaw('min_price ASC NULLS LAST');
        } elseif ($tri === 'prix_desc') {
            $query->orderByRaw('min_price DESC NULLS LAST');
        } else {
            $query->orderBy('nomresort');
        }

        $resorts = $query->paginate(12)->appends($request->query());

        return view('resorts', compact('resorts', 'typeclubs', 'localisations', 'paysList', 'activitesList', 'regroupementsList'));
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resort;
use App\Models\Tarifer;
use App\Models\Periode;
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
            ->selectRaw('CAST(MIN(tarifer.prix) + (resort.numresort * 7) + (resort.nbtridents * 50) AS DECIMAL(10,0))')
            ->whereColumn('proposer.numresort', 'resort.numresort');

        $query->selectSub($priceSubquery, 'min_price');

        $query->with(['avis' => function($q) {
            $q->select('numavis', 'numresort', 'noteavis');
        }]);

        $query->when($typeclub, function($q, $val) { return $q->whereHas('typeclubs', function($sub) use ($val) { $sub->where('typeclub.numtypeclub', $val); }); });
        $query->when($localisation, function($q, $val) { return $q->whereHas('localisations', function($sub) use ($val) { $sub->where('localisation.numlocalisation', $val); }); });
        $query->when($pays, function($q, $val) { return $q->where('resort.codepays', $val); });
        $query->when($activite, function($q, $val) { return $q->whereHas('typesActivites', function($sub) use ($val) { $sub->where('typeactivite.numtypeactivite', $val); }); });
        $query->when($regroupement, function($q, $val) { return $q->whereHas('regroupements', function($sub) use ($val) { $sub->where('regroupementclub.numregroupement', $val); }); });

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

    public function getPrix(Request $request)
    {
        $numresort = $request->input('numresort');
        $numtype = $request->input('numtype');
        $dateDebut = $request->input('dateDebut');

        $dateReference = $dateDebut ? $dateDebut : now()->format('Y-m-d');
        
        $periode = Periode::where('datedebutperiode', '<=', $dateReference)
                          ->where('datefinperiode', '>=', $dateReference)
                          ->first();

        if (!$periode) {
            $periode = Periode::first(); 
        }

        $tarif = Tarifer::where('numresort', $numresort)
                        ->where('numtype', $numtype)
                        ->where('numperiode', $periode ? $periode->numperiode : 1)
                        ->first();

        if ($tarif) {
            // Utilisation de la colonne 'prix'
            $prixStandard = $tarif->prix;
            $prixFinal = $tarif->prix;
            $hasPromo = false;

            // Vérification promo par rapport à 'prix'
            if ($tarif->prix_promo && $tarif->prix_promo > 0 && $tarif->prix_promo < $tarif->prix) {
                $prixFinal = $tarif->prix_promo;
                $hasPromo = true;
            }
        } else {
            // Fallback si pas de tarif en base
            $resort = Resort::find($numresort);
            $tridents = $resort ? $resort->nbtridents : 3;

            $prixBaseChambre = match ((int)$numtype) {
                1 => 200, 
                2 => 300, 
                3 => 500, 
                4 => 250, 
                default => 250,
            };

            $prixCalcule = $prixBaseChambre + ($tridents * 50);
            $prixStandard = $prixCalcule;
            $prixFinal = $prixCalcule;
            $hasPromo = false;
        }

        return response()->json([
            'prixParNuit' => $prixFinal,
            'prixStandard' => $prixStandard,
            'hasPromo' => $hasPromo
        ]);
    }
}
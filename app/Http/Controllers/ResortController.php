<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Resort;
use App\Models\Pays;
use App\Models\DomaineSkiable;
use App\Models\Documentation;
use App\Models\Photo;
use App\Models\Restaurant;
use App\Models\Tarifer; 
use App\Models\Periode; 
use App\Models\TypeChambre;
use App\Models\RegroupementClub;
use App\Models\TypeActivite;

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

    public function create()
    {
        $paysList = Pays::orderBy('nompays')->get();
        $domainesList = DomaineSkiable::orderBy('nomdomaine')->get();
        $docsList = Documentation::all();
        $typesChambre = TypeChambre::orderBy('nomtype')->get();
        $groupesList = RegroupementClub::orderBy('nomregroupement')->get();

        return view('resort.create', compact(
            'paysList', 'domainesList', 'docsList', 'typesChambre', 'groupesList'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomresort' => 'required|string|max:255',
            'codepays' => 'required|exists:pays,codepays',
            'nbtridents' => 'required|integer|min:3|max:5',
            'descriptionresort' => 'nullable|string',
            'is_ski' => 'nullable',
            'numdomaine' => 'nullable|exists:domaineskiable,numdomaine',
            'latituderesort' => 'nullable|numeric',
            'longituderesort' => 'nullable|numeric',
            'groupes' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,webp|max:4096',
            'restaurants' => 'nullable|array',
            'restaurants.*.nom' => 'required_with:restaurants|string',
            'restaurants.*.type' => 'nullable|in:Gourmet,Buffet,Snack,Bar',
            'restaurants.*.description' => 'nullable|string',
            'chambres' => 'nullable|array',
            'chambres.*.active' => 'nullable',
            'chambres.*.quantite' => 'nullable|integer|min:0',
            'chambres.*.prix' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();
            $resort = new Resort();
            $resort->nomresort = $request->nomresort;
            $resort->codepays = $request->codepays;
            $resort->nbtridents = $request->nbtridents;
            $resort->descriptionresort = $request->descriptionresort;
            $resort->latituderesort = $request->latituderesort;
            $resort->longituderesort = $request->longituderesort;
            $resort->numdocumentation = $request->numdocumentation;
            $totalChambresCalcul = 0;
            if ($request->has('chambres')) {
                foreach ($request->chambres as $c) {
                    if (isset($c['active']) && isset($c['quantite'])) {
                        $totalChambresCalcul += intval($c['quantite']);
                    }
                }
            }
            $resort->nbchambrestotal = $totalChambresCalcul > 0 ? $totalChambresCalcul : ($request->nbchambrestotal ?? 0);

            if ($request->has('is_ski') && $request->filled('numdomaine')) {
                $resort->numdomaine = $request->numdomaine;
            } else {
                $resort->numdomaine = null;
            }

            $resort->save();

            if ($request->has('groupes')) {
                $resort->regroupements()->attach($request->groupes);
            }

            if ($request->hasFile('photos')) {
                $slugResort = Str::slug($resort->nomresort, '');

                foreach ($request->file('photos') as $index => $file) {
                    $filename = $slugResort;
                    if ($index > 0) {
                        $filename .= '-' . ($index + 1);
                    }
                    if (file_exists(public_path('img/ressort/' . $filename . '.webp'))) {
                        $filename .= '-' . time();
                    }
                    $filename .= '.webp';

                    $image = @imagecreatefromstring(file_get_contents($file));
                    if ($image !== false) {
                        imagewebp($image, public_path('img/ressort/' . $filename), 80);
                        imagedestroy($image);

                        Photo::create([
                            'numresort' => $resort->numresort,
                            'lienphoto' => $filename
                        ]);
                    }
                }
            }

            if ($request->filled('restaurants')) {
                foreach ($request->restaurants as $restoData) {
                    if (!empty($restoData['nom'])) {
                        Restaurant::create([
                            'numresort' => $resort->numresort,
                            'nomrestaurant' => $restoData['nom'],
                            'typerestaurant' => $restoData['type'] ?? null,
                            'descriptionrestaurant' => $restoData['description'] ?? null
                        ]);
                    }
                }
            }

            $periodeDefaut = Periode::first();
            if (!$periodeDefaut) {
                $periodeDefaut = Periode::create([
                    'datedebutperiode' => now(),
                    'datefinperiode' => now()->addYear(),
                    'typesaison' => 'Standard'
                ]);
            }

            if ($request->has('chambres')) {
                foreach ($request->chambres as $typeId => $data) {
                    if (isset($data['active']) && $data['active'] == 1) {
                        
                        $quantite = intval($data['quantite'] ?? 0);
                        $prix = floatval($data['prix'] ?? 0);

                        if ($quantite > 0) {
                            $resort->typechambres()->attach($typeId, ['nbchambres' => $quantite]);
                        }

                        if ($prix > 0) {
                            Tarifer::create([
                                'numresort' => $resort->numresort,
                                'numtype' => $typeId,
                                'numperiode' => $periodeDefaut->numperiode,
                                'prix' => $prix,
                                'prix_promo' => null
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return redirect('/')->with('success', "Le processus de création pour le séjour '{$resort->nomresort}' a été lancé avec succès. (Données enregistrées).");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => "Erreur technique : " . $e->getMessage()]);
        }
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
            $prixStandard = $tarif->prix;
            $prixFinal = $tarif->prix;
            $hasPromo = false;

            if ($tarif->prix_promo && $tarif->prix_promo > 0 && $tarif->prix_promo < $tarif->prix) {
                $prixFinal = $tarif->prix_promo;
                $hasPromo = true;
            }
        } else {
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
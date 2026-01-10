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
use App\Models\Activite;
use App\Models\Partenaire;

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

        // Si la colonne est_valide existe, on filtre pour le public
        if (\Schema::hasColumn('resort', 'est_valide')) {
            $query->where('est_valide', true);
        }

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

    public function create()
    {
        $paysList = \App\Models\Pays::orderBy('nompays')->get();
        $domainesList = \App\Models\DomaineSkiable::orderBy('nomdomaine')->get();
        $docsList = \App\Models\Documentation::all();
        $groupesList = \App\Models\RegroupementClub::orderBy('nomregroupement')->get();

        return view('resort.create', compact('paysList', 'domainesList', 'docsList', 'groupesList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomresort' => 'required|string|max:255',
            'codepays' => 'required|exists:pays,codepays',
            'photos.*' => 'image|mimes:jpeg,png,jpg,webp|max:4096',
            'restaurants' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            $resort = new Resort();
            $resort->nomresort = $request->nomresort;
            $resort->codepays = $request->codepays;
            $resort->nbtridents = $request->nbtridents ?? 3;
            $resort->descriptionresort = $request->descriptionresort;
            $resort->latituderesort = $request->latituderesort;
            $resort->longituderesort = $request->longituderesort;
            $resort->numdocumentation = $request->numdocumentation;
            $resort->nbchambrestotal = 0; 
            
            if (\Schema::hasColumn('resort', 'est_valide')) {
                $resort->est_valide = false;
            }

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
                // Slug sans séparateur, ex: "Afrique du Sex" -> "afriquedusex"
                $slugResort = Str::slug($resort->nomresort, ''); 
                
                foreach ($request->file('photos') as $index => $file) {
                    $filename = $slugResort;
                    // Si plusieurs photos, on indexe à partir de la 2ème (index 1)
                    if ($index > 0) {
                        $filename .= $index;
                    }
                    $filename .= '.webp';

                    $image = @imagecreatefromstring(file_get_contents($file));
                    if ($image !== false) {
                        imagewebp($image, public_path('img/ressort/' . $filename), 80);
                        imagedestroy($image);
                        
                        Photo::create([
                            'numresort' => $resort->numresort, 
                            'nomfichierphoto' => $filename,
                            'formatphoto' => 'webp',
                            'taillephoto' => $file->getSize()
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

            Partenaire::firstOrCreate(
                ['nompartenaire' => 'Service ' . $resort->nomresort],
                ['emailpartenaire' => 'activites@' . Str::slug($resort->nomresort) . '.clubmed.com']
            );

            DB::commit();

            if ($request->input('action') === 'save_exit') {
                return redirect()->route('marketing.dashboard')
                                 ->with('success', "Le brouillon pour '{$resort->nomresort}' a été sauvegardé.");
            }

            return redirect()->route('resort.step2', $resort->numresort)
                             ->with('success', "Structure créée ! Passons maintenant aux hébergements.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => "Erreur : " . $e->getMessage()]);
        }
    }

    public function editStructure($id)
    {
        $resort = Resort::with(['regroupements', 'restaurants'])->findOrFail($id);
        
        $paysList = \App\Models\Pays::orderBy('nompays')->get();
        $domainesList = \App\Models\DomaineSkiable::orderBy('nomdomaine')->get();
        $docsList = \App\Models\Documentation::all();
        $groupesList = \App\Models\RegroupementClub::orderBy('nomregroupement')->get();

        return view('resort.edit_structure', compact(
            'resort', 'paysList', 'domainesList', 'docsList', 'groupesList'
        ));
    }

    public function updateStructure(Request $request, $id)
    {
        $resort = Resort::findOrFail($id);

        $request->validate([
            'nomresort' => 'required|string|max:255',
            'codepays' => 'required|exists:pays,codepays',
            'photos.*' => 'image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        try {
            DB::beginTransaction();

            $resort->nomresort = $request->nomresort;
            $resort->codepays = $request->codepays;
            $resort->nbtridents = $request->nbtridents ?? 3;
            $resort->descriptionresort = $request->descriptionresort;
            $resort->latituderesort = $request->latituderesort;
            $resort->longituderesort = $request->longituderesort;

            if ($request->has('is_ski') && $request->filled('numdomaine')) {
                $resort->numdomaine = $request->numdomaine;
            } else {
                $resort->numdomaine = null;
            }

            $resort->save();

            if ($request->has('groupes')) {
                $resort->regroupements()->sync($request->groupes);
            } else {
                $resort->regroupements()->detach();
            }

            if ($request->hasFile('photos')) {
                // Slug sans séparateur
                $slugResort = Str::slug($resort->nomresort, ''); 
                
                foreach ($request->file('photos') as $index => $file) {
                    $filename = $slugResort;
                    
                    if ($index > 0) {
                        $filename .= $index;
                    }
                    $filename .= '.webp';

                    $image = @imagecreatefromstring(file_get_contents($file));
                    if ($image !== false) {
                        imagewebp($image, public_path('img/ressort/' . $filename), 80);
                        imagedestroy($image);
                        
                        $exists = Photo::where('numresort', $resort->numresort)
                                       ->where('nomfichierphoto', $filename)
                                       ->exists();
                        
                        if (!$exists) {
                            Photo::create([
                                'numresort' => $resort->numresort, 
                                'nomfichierphoto' => $filename,
                                'formatphoto' => 'webp',
                                'taillephoto' => $file->getSize()
                            ]);
                        }
                    }
                }
            }

            if ($request->filled('restaurants')) {
                Restaurant::where('numresort', $resort->numresort)->delete();
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

            $partnerName = 'Service ' . $resort->nomresort;
            Partenaire::firstOrCreate(
                ['nompartenaire' => $partnerName],
                ['emailpartenaire' => 'activites@' . Str::slug($resort->nomresort) . '.clubmed.com']
            );

            DB::commit();

            if ($request->input('action') === 'save_exit') {
                return redirect()->route('marketing.dashboard')->with('success', "Modifications enregistrées.");
            }

            return redirect()->route('resort.step2', $resort->numresort)
                             ->with('success', "Structure mise à jour !");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => "Erreur : " . $e->getMessage()]);
        }
    }

    public function createAccommodation($id)
    {
        $resort = Resort::findOrFail($id);
        $typesChambre = TypeChambre::orderBy('nomtype')->get();
        
        $existingData = [];
        foreach ($resort->typechambres as $tc) {
            $existingData[$tc->numtype]['active'] = true;
            $existingData[$tc->numtype]['quantite'] = $tc->pivot->nbchambres;
        }
        $tarifs = Tarifer::where('numresort', $id)->get();
        foreach ($tarifs as $t) {
            $existingData[$t->numtype]['active'] = true;
        }

        return view('resort.step2_accommodation', compact('resort', 'typesChambre', 'existingData'));
    }

    public function storeAccommodation(Request $request, $id)
    {
        $resort = Resort::findOrFail($id);

        $request->validate([
            'chambres' => 'required|array',
            'chambres.*.active' => 'nullable',
            'chambres.*.quantite' => 'nullable|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            $totalChambres = 0;
            
            foreach ($request->chambres as $typeId => $data) {
                if (isset($data['active']) && $data['active'] == 1) {
                    $quantite = intval($data['quantite'] ?? 0);

                    $resort->typechambres()->detach($typeId);
                    if ($quantite > 0) {
                        $resort->typechambres()->attach($typeId, ['nbchambres' => $quantite]);
                        $totalChambres += $quantite;
                    }
                } else {
                    $resort->typechambres()->detach($typeId);
                }
            }

            $resort->nbchambrestotal = $totalChambres;
            $resort->save();

            DB::commit();

            if ($request->input('action') === 'save_exit') {
                return redirect()->route('marketing.dashboard')
                                 ->with('success', "Capacités sauvegardées pour '{$resort->nomresort}'.");
            }

            return redirect()->route('resort.step3', $resort->numresort)
                             ->with('success', "Hébergements configurés ! Passons aux activités.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => "Erreur : " . $e->getMessage()]);
        }
    }

    public function createActivities($id)
    {
        $resort = Resort::findOrFail($id);
        $typesActivites = TypeActivite::orderBy('nomtypeactivite')->get();
        $globalActivities = Activite::with('typeActivite')->orderBy('nomactivite')->get();

        $partnerName = 'Service ' . $resort->nomresort;
        $partner = Partenaire::where('nompartenaire', $partnerName)->first();

        $resortActivities = collect();
        if ($partner) {
            $resortActivities = DB::table('activite')
                ->join('fourni', 'activite.numactivite', '=', 'fourni.numactivite')
                ->leftJoin('activitealacarte', 'activite.numactivite', '=', 'activitealacarte.numactivite')
                ->join('typeactivite', 'activite.numtypeactivite', '=', 'typeactivite.numtypeactivite')
                ->where('fourni.numpartenaire', $partner->numpartenaire)
                ->select(
                    'activite.numactivite',
                    'activite.nomactivite',
                    'activite.estincluse', 
                    'typeactivite.nomtypeactivite',
                    'activitealacarte.prixmin as prix_carte' 
                )
                ->get();
        }

        return view('resort.step3_activities', compact('resort', 'typesActivites', 'resortActivities', 'globalActivities'));
    }

    public function storeActivities(Request $request, $id)
    {
        $resort = Resort::findOrFail($id);

        $request->validate([
            'selected_activities' => 'nullable|array',
            'selected_activities.*' => 'exists:activite,numactivite',
            'new_activities' => 'nullable|array',
            'new_activities.*.nom' => 'required_with:new_activities|string|max:255',
            'new_activities.*.type' => 'required_with:new_activities|exists:typeactivite,numtypeactivite',
            'new_activities.*.inclus' => 'required_with:new_activities|in:1,0',
        ]);

        try {
            DB::beginTransaction();

            $partner = Partenaire::firstOrCreate(
                ['nompartenaire' => 'Service ' . $resort->nomresort],
                ['emailpartenaire' => 'contact@' . Str::slug($resort->nomresort) . '.com']
            );

            $typesToSync = [];

            if ($request->filled('selected_activities')) {
                foreach ($request->selected_activities as $actId) {
                    $exists = DB::table('fourni')->where('numpartenaire', $partner->numpartenaire)->where('numactivite', $actId)->exists();
                    if (!$exists) {
                        DB::table('fourni')->insert([
                            'numpartenaire' => $partner->numpartenaire,
                            'numactivite' => $actId
                        ]);
                    }
                    $act = Activite::find($actId);
                    if($act) $typesToSync[] = $act->numtypeactivite;
                }
            }

            if ($request->filled('new_activities')) {
                foreach ($request->new_activities as $newAct) {
                    if (!empty($newAct['nom']) && !empty($newAct['type'])) {
                        
                        $idAct = DB::table('activite')->insertGetId([
                            'numtypeactivite' => $newAct['type'],
                            'nomactivite' => $newAct['nom'],
                            'descriptionactivite' => $newAct['description'] ?? null,
                            'dureeactivite' => 60,
                            'agemin' => 0,
                            'estincluse' => ($newAct['inclus'] == '1')
                        ], 'numactivite');

                        DB::table('fourni')->insert([
                            'numpartenaire' => $partner->numpartenaire,
                            'numactivite' => $idAct
                        ]);

                        if ($newAct['inclus'] == '0') {
                            DB::table('activitealacarte')->insert([
                                'numactivite' => $idAct,
                                'prixmin' => 20 
                            ]);
                        }
                        
                        $typesToSync[] = $newAct['type'];
                    }
                }
            }

            $currentTypes = DB::table('fourni')
                ->join('activite', 'fourni.numactivite', '=', 'activite.numactivite')
                ->where('fourni.numpartenaire', $partner->numpartenaire)
                ->pluck('activite.numtypeactivite')
                ->toArray();
            
            $resort->typesActivites()->sync(array_unique(array_merge($currentTypes, $typesToSync)));

            DB::commit();

            if ($request->input('action') === 'save_exit') {
                return redirect()->route('marketing.dashboard')->with('success', "Activités sauvegardées.");
            }

            return redirect()->route('resort.step4', $resort->numresort)
                             ->with('success', "Activités enregistrées ! Configurez maintenant les tarifs.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => "Erreur : " . $e->getMessage()]);
        }
    }

    public function destroyActivity($id, $activityId)
    {
        $resort = Resort::findOrFail($id);
        $partnerName = 'Service ' . $resort->nomresort;
        $partner = Partenaire::where('nompartenaire', $partnerName)->firstOrFail();

        DB::table('fourni')
            ->where('numpartenaire', $partner->numpartenaire)
            ->where('numactivite', $activityId)
            ->delete();

        $remainingTypes = DB::table('fourni')
                ->join('activite', 'fourni.numactivite', '=', 'activite.numactivite')
                ->where('fourni.numpartenaire', $partner->numpartenaire)
                ->pluck('activite.numtypeactivite')
                ->toArray();

        $resort->typesActivites()->sync(array_unique($remainingTypes));

        return back()->with('success', "Activité retirée du resort.");
    }

    public function createPricing($id)
    {
        $resort = Resort::findOrFail($id);
        $periodes = Periode::orderBy('datedebutperiode')->get();
        $activeRooms = $resort->typechambres;

        $existingPrices = [];
        $tarifs = Tarifer::where('numresort', $id)->get();
        foreach ($tarifs as $t) {
            $existingPrices[$t->numtype][$t->numperiode] = $t->prix;
        }

        return view('resort.step4_pricing', compact('resort', 'periodes', 'activeRooms', 'existingPrices'));
    }

    public function storePricing(Request $request, $id)
    {
        $resort = Resort::findOrFail($id);
        
        $prices = $request->input('prix', []);

        try {
            DB::beginTransaction();

            foreach ($prices as $typeId => $periodeData) {
                foreach ($periodeData as $periodeId => $montant) {
                    if ($montant !== null && $montant > 0) {
                        Tarifer::where('numresort', $id)
                               ->where('numtype', $typeId)
                               ->where('numperiode', $periodeId)
                               ->delete();

                        Tarifer::create([
                            'numresort' => $id,
                            'numtype' => $typeId,
                            'numperiode' => $periodeId,
                            'prix' => $montant,
                            'prix_promo' => null
                        ]);
                    }
                }
            }

            DB::commit();

            if ($request->input('action') === 'save_exit') {
                return redirect()->route('marketing.dashboard')->with('success', "Tarifs sauvegardés.");
            }

            return redirect()->route('marketing.dashboard')
                             ->with('success', "Félicitations ! Le séjour au {$resort->nomresort} est configuré et prêt à être validé par le Directeur.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
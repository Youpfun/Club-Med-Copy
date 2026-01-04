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

    // =========================================================
    // ÉTAPE 1 : STRUCTURE
    // =========================================================

    public function create()
    {
        $paysList = Pays::orderBy('nompays')->get();
        $domainesList = DomaineSkiable::orderBy('nomdomaine')->get();
        $docsList = Documentation::all();
        $groupesList = RegroupementClub::orderBy('nomregroupement')->get();

        return view('resort.create', compact('paysList', 'domainesList', 'docsList', 'groupesList'));
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
            $resort->nbchambrestotal = 0; 

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
                    if ($index > 0) $filename .= '-' . $index;
                    $filename .= '.webp';

                    $image = @imagecreatefromstring(file_get_contents($file));
                    if ($image !== false) {
                        imagewebp($image, public_path('img/ressort/' . $filename), 80);
                        imagedestroy($image);
                        Photo::create(['numresort' => $resort->numresort, 'lienphoto' => $filename]);
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

            DB::commit();

            return redirect()->route('resort.step2', $resort->numresort)
                             ->with('success', "Structure créée ! Passons maintenant aux hébergements.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => "Erreur : " . $e->getMessage()]);
        }
    }

    // =========================================================
    // ÉTAPE 2 : HÉBERGEMENT
    // =========================================================

    public function createAccommodation($id)
    {
        $resort = Resort::findOrFail($id);
        $typesChambre = TypeChambre::orderBy('nomtype')->get();
        return view('resort.step2_accommodation', compact('resort', 'typesChambre'));
    }

    public function storeAccommodation(Request $request, $id)
    {
        $resort = Resort::findOrFail($id);

        $request->validate([
            'chambres' => 'required|array',
            'chambres.*.active' => 'nullable',
            'chambres.*.quantite' => 'nullable|integer|min:0',
            'chambres.*.prix' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $totalChambres = 0;
            $periodeDefaut = Periode::first();
            if (!$periodeDefaut) {
                $periodeDefaut = Periode::create(['datedebutperiode' => now(), 'datefinperiode' => now()->addYear(), 'typesaison' => 'Standard']);
            }

            foreach ($request->chambres as $typeId => $data) {
                if (isset($data['active']) && $data['active'] == 1) {
                    $quantite = intval($data['quantite'] ?? 0);
                    $prix = floatval($data['prix'] ?? 0);

                    $resort->typechambres()->detach($typeId);
                    if ($quantite > 0) {
                        $resort->typechambres()->attach($typeId, ['nbchambres' => $quantite]);
                        $totalChambres += $quantite;
                    }

                    Tarifer::where('numresort', $id)->where('numtype', $typeId)->delete();
                    if ($prix > 0) {
                        Tarifer::create([
                            'numresort' => $id,
                            'numtype' => $typeId,
                            'numperiode' => $periodeDefaut->numperiode,
                            'prix' => $prix,
                            'prix_promo' => null 
                        ]);
                    }
                }
            }

            $resort->nbchambrestotal = $totalChambres;
            $resort->save();

            DB::commit();

            return redirect()->route('resort.step3', $resort->numresort)
                             ->with('success', "Hébergements configurés ! Passons aux activités.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => "Erreur : " . $e->getMessage()]);
        }
    }

    // =========================================================
    // ÉTAPE 3 : ACTIVITÉS (Logique Hybride : Selection ou Création)
    // =========================================================

    public function createActivities($id)
    {
        $resort = Resort::findOrFail($id);
        
        // 1. Liste complète des Activités existantes (Catalogue Global) pour la sélection
        $allActivities = Activite::with('typeActivite')->orderBy('nomactivite')->get();
        
        // 2. Liste des Types pour la création d'une nouvelle activité
        $typesActivites = TypeActivite::orderBy('nomtypeactivite')->get();
        
        // 3. Types déjà activés pour ce resort (pour pré-cocher ou afficher)
        $currentTypeIds = $resort->typesActivites->pluck('numtypeactivite')->toArray();

        return view('resort.step3_activities', compact('resort', 'allActivities', 'typesActivites', 'currentTypeIds'));
    }

    public function storeActivities(Request $request, $id)
    {
        $resort = Resort::findOrFail($id);

        // 1. Validation stricte des deux types de données
        $request->validate([
            // A. Les cases à cocher (IDs existants)
            'selected_activities' => 'nullable|array',
            'selected_activities.*' => 'exists:activite,numactivite',
            
            // B. Les nouvelles lignes ajoutées via JS
            'new_activities' => 'nullable|array',
            'new_activities.*.type' => 'required_with:new_activities|exists:typeactivite,numtypeactivite',
            'new_activities.*.nom' => 'required_with:new_activities|string|max:255',
            'new_activities.*.description' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Liste pour collecter tous les types d'activités (pour la table 'partager')
            $typesToSync = [];

            // ÉTAPE A : Traiter les activités existantes sélectionnées
            if ($request->filled('selected_activities')) {
                // On récupère les infos des activités cochées pour trouver leur type
                $existingActs = Activite::whereIn('numactivite', $request->selected_activities)->get();
                foreach ($existingActs as $act) {
                    $typesToSync[] = $act->numtypeactivite;
                }
            }

            // ÉTAPE B : Créer les nouvelles activités
            if ($request->filled('new_activities')) {
                foreach ($request->new_activities as $newAct) {
                    // On ne crée que si le nom et le type sont remplis
                    if (!empty($newAct['nom']) && !empty($newAct['type'])) {
                        
                        Activite::create([
                            'numresort' => $resort->numresort, // Lien vers le resort (via la colonne qu'on a ajoutée)
                            'numtypeactivite' => $newAct['type'],
                            'nomactivite' => $newAct['nom'],
                            'descriptionactivite' => $newAct['description'] ?? null,
                            // Valeurs par défaut obligatoires en BDD
                            'dureeactivite' => 60, // Ex: 60 min par défaut
                            'agemin' => 0,
                            'estincluse' => true
                        ]);

                        // On ajoute ce type à la liste à synchroniser
                        $typesToSync[] = $newAct['type'];
                    }
                }
            }

            // ÉTAPE C : Mettre à jour la table de liaison Resort <-> TypeActivite (table 'partager')
            // Cela permet d'afficher les icônes sur la fiche resort
            if (!empty($typesToSync)) {
                $uniqueTypes = array_unique($typesToSync);
                $resort->typesActivites()->sync($uniqueTypes);
            } else {
                // Si rien n'est sélectionné/créé, on détache tout
                $resort->typesActivites()->detach();
            }

            DB::commit();

            return redirect()->route('marketing.dashboard')
                             ->with('success', "Félicitations ! Le séjour au {$resort->nomresort} est entièrement configuré.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => "Erreur technique : " . $e->getMessage()]);
        }
    }
}
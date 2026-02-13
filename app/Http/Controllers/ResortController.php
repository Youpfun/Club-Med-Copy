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
use Illuminate\Support\Facades\Schema;

class ResortController extends Controller
{
    // =========================================================================
    // PARTIE 1 : LISTING PUBLIC ET API PRIX
    // =========================================================================

    public function index(Request $request)
    {
        $resorts = Resort::all();
        dd($resorts);
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
        if (Schema::hasColumn('resort', 'est_valide')) {
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

    // =========================================================================
    // PARTIE 2 : WORKFLOW DE CRÉATION (ÉTAPE 1 - STRUCTURE)
    // =========================================================================

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
            
            if (Schema::hasColumn('resort', 'est_valide')) {
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

            // Gestion Photos : minuscules sans espace
            if ($request->hasFile('photos')) {
                // EX: "Afrique du Sud" -> "afriquedusud"
                $slugResort = strtolower(str_replace(' ', '', $resort->nomresort)); 
                
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
                                 ->with('success', "Le brouillon pour '{$resort->nomresort}' a été sauvegardé (Statut : Incomplet).");
            }

            return redirect()->route('resort.step2', $resort->numresort)
                             ->with('success', "Structure créée ! Passons aux hébergements.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => "Erreur : " . $e->getMessage()]);
        }
    }

    public function editStructure($id)
    {
        $resort = Resort::with(['regroupements', 'restaurants'])->findOrFail($id);
        $paysList = Pays::orderBy('nompays')->get();
        $domainesList = DomaineSkiable::orderBy('nomdomaine')->get();
        $docsList = Documentation::all();
        $groupesList = RegroupementClub::orderBy('nomregroupement')->get();

        return view('resort.edit_structure', compact('resort', 'paysList', 'domainesList', 'docsList', 'groupesList'));
    }

    public function updateStructure(Request $request, $id)
    {
        $resort = Resort::findOrFail($id);
        $request->validate(['nomresort' => 'required|string|max:255']);

        try {
            DB::beginTransaction();

            // 1. Capturer l'ancien nom AVANT la modification pour le renommage
            $oldName = $resort->nomresort; 

            // 2. Mise à jour des infos
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

            // 3. RENOMMAGE DES PHOTOS ET DU PARTENAIRE
            if ($oldName !== $request->nomresort) {
                // On utilise la convention "tout attaché minuscule"
                $oldSlug = strtolower(str_replace(' ', '', $oldName));       // ex: afriquedusex
                $newSlug = strtolower(str_replace(' ', '', $request->nomresort)); // ex: afriquetour

                $photos = Photo::where('numresort', $resort->numresort)->get();

                foreach($photos as $photo) {
                    $oldFilename = $photo->nomfichierphoto; // ex: afriquedusex.webp

                    // Si le nom du fichier contient l'ancien slug, on le remplace par le nouveau
                    if (strpos($oldFilename, $oldSlug) !== false) {
                        $newFilename = str_replace($oldSlug, $newSlug, $oldFilename); // ex: afriquetour.webp

                        // Chemins complets vers les fichiers
                        $oldPath = public_path('img/ressort/' . $oldFilename);
                        $newPath = public_path('img/ressort/' . $newFilename);

                        // Renommage physique du fichier sur le disque
                        if (file_exists($oldPath)) {
                            rename($oldPath, $newPath);
                        }

                        // Mise à jour du nom dans la base de données
                        $photo->nomfichierphoto = $newFilename;
                        $photo->save();
                    }
                }

                // Mise à jour du Partenaire pour garder la cohérence "Service [Nom]"
                $oldPartnerName = 'Service ' . $oldName;
                $newPartnerName = 'Service ' . $request->nomresort;
                // Pour l'email, on garde le slug propre de Laravel pour éviter les caractères spéciaux
                $newEmail = 'activites@' . Str::slug($request->nomresort) . '.clubmed.com'; 

                Partenaire::where('nompartenaire', $oldPartnerName)
                    ->update([
                        'nompartenaire' => $newPartnerName,
                        'emailpartenaire' => $newEmail
                    ]);
            }
            // ========================================================

            if ($request->has('groupes')) {
                $resort->regroupements()->sync($request->groupes);
            } else {
                $resort->regroupements()->detach();
            }

            // Ajout de nouvelles photos si envoyées
            if ($request->hasFile('photos')) {
                $slugResort = strtolower(str_replace(' ', '', $resort->nomresort)); 
                
                foreach ($request->file('photos') as $index => $file) {
                    $filename = $slugResort;
                    
                    // On vérifie combien on a déjà de photos pour incrémenter correctement
                    $existingCount = Photo::where('numresort', $resort->numresort)->count();
                    $suffix = ($index + $existingCount > 0) ? ($index + $existingCount) : '';
                    
                    if ($suffix) $filename .= $suffix;
                    $filename .= '.webp';

                    $image = @imagecreatefromstring(file_get_contents($file));
                    if ($image !== false) {
                        imagewebp($image, public_path('img/ressort/' . $filename), 80);
                        imagedestroy($image);
                        
                        // On évite les doublons en DB
                        if (!Photo::where('numresort', $resort->numresort)->where('nomfichierphoto', $filename)->exists()) {
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

            // On s'assure que le partenaire existe (ou on le met à jour via le bloc rename ci-dessus)
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

    // =========================================================================
    // PARTIE 3 : ÉTAPE 2 - HÉBERGEMENT
    // =========================================================================

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

    // =========================================================================
    // PARTIE 4 : ÉTAPE 3 - ACTIVITÉS
    // =========================================================================

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
                ->join('typeactivite', 'activite.numtypeactivite', '=', 'typeactivite.numtypeactivite')
                ->where('fourni.numpartenaire', $partner->numpartenaire)
                ->select(
                    'activite.*', 
                    'typeactivite.nomtypeactivite',
                    'fourni.est_incluse as pivot_inclus',
                    'fourni.prix as pivot_prix'
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
            'new_activities' => 'nullable|array',
            'activities_config' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            $partner = Partenaire::firstOrCreate(
                ['nompartenaire' => 'Service ' . $resort->nomresort],
                ['emailpartenaire' => 'contact@' . Str::slug($resort->nomresort) . '.clubmed.com']
            );

            $typesToSync = [];

            // 1. AJOUT DEPUIS CATALOGUE (SELECTED)
            if ($request->filled('selected_activities')) {
                foreach ($request->selected_activities as $actId) {
                    $exists = DB::table('fourni')->where('numpartenaire', $partner->numpartenaire)->where('numactivite', $actId)->exists();
                    if (!$exists) {
                        $original = Activite::find($actId);
                        $estIncluse = $original ? $original->estincluse : true;

                        DB::table('fourni')->insert([
                            'numpartenaire' => $partner->numpartenaire,
                            'numactivite' => $actId,
                            'est_incluse' => $estIncluse ? true : false,
                            'prix' => $estIncluse ? null : 20.00
                        ]);
                    }
                    $act = Activite::find($actId);
                    if($act) $typesToSync[] = $act->numtypeactivite;
                }
            }

            // 2. CRÉATION NOUVELLES ACTIVITÉS (MANUELLES)
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
                            'numactivite' => $idAct,
                            'est_incluse' => ($newAct['inclus'] == '1') ? true : false,
                            'prix' => ($newAct['inclus'] == '0') ? ($newAct['prix'] ?? 20) : null
                        ]);
                        
                        $typesToSync[] = $newAct['type'];
                    }
                }
            }

            // 3. MISE À JOUR CONFIGURATION (INCLUS / PRIX)
            if ($request->filled('activities_config')) {
                foreach ($request->activities_config as $actId => $config) {
                    $isInclus = isset($config['inclus']) && $config['inclus'] == '1';
                    $prix = $isInclus ? null : ($config['prix'] ?? 20);

                    if (Schema::hasColumn('fourni', 'est_incluse')) {
                        DB::table('fourni')
                            ->where('numpartenaire', $partner->numpartenaire)
                            ->where('numactivite', $actId)
                            ->update([
                                'est_incluse' => $isInclus ? true : false,
                                'prix' => $prix
                            ]);
                    }
                    
                    $act = Activite::find($actId);
                    if($act) $typesToSync[] = $act->numtypeactivite;
                }
            }

            // Sync des types
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

    // =========================================================================
    // PARTIE 5 : ÉTAPE 4 - TARIFS ET PUBLICATION
    // =========================================================================

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

            if ($request->input('action') === 'finish') {
                if (Schema::hasColumn('resort', 'est_valide')) {
                    $resort->est_valide = true;
                    $resort->save();
                }
                
                DB::commit();

                return redirect()->route('marketing.dashboard')
                             ->with('success', "Félicitations ! Le séjour au {$resort->nomresort} est maintenant **PUBLIÉ** et visible par les clients.");
            }

            DB::commit();

            if ($request->input('action') === 'save_exit') {
                return redirect()->route('marketing.dashboard')->with('success', "Tarifs sauvegardés (Séjour toujours en brouillon).");
            }

            return redirect()->route('marketing.dashboard')
                             ->with('success', "Modifications enregistrées.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
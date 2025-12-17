<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tarifer;
use App\Models\Periode;
use App\Models\TypeChambre;
use App\Models\Resort;
use App\Models\TypeClub;
use Illuminate\Support\Facades\DB;

class MarketingController extends Controller
{
    public function index(Request $request)
    {
        $typeClubs = TypeClub::all();
        $periodes = Periode::orderBy('datedebutperiode')->get();
        $typesChambre = TypeChambre::all(); 

        $query = Resort::query();
        if ($request->filled('type_club')) {
            $query->whereHas('typeClubs', function($q) use ($request) {
                $q->where('typeclub.numtypeclub', $request->type_club);
            });
        }
        $resorts = $query->orderBy('nomresort')->get();

        $selectedResortId = $request->input('numresort');
        $selectedResort = $selectedResortId ? Resort::find($selectedResortId) : null;
        $stats = [];

        if ($selectedResort) {
            $typesProposes = DB::table('proposer')
                               ->where('numresort', $selectedResort->numresort)
                               ->pluck('numtype')
                               ->toArray();

            foreach ($periodes as $p) {
                foreach ($typesChambre as $tc) {
                    if (!in_array($tc->numtype, $typesProposes)) continue;

                    $tarif = DB::table('tarifer')
                               ->where('numperiode', $p->numperiode)
                               ->where('numtype', $tc->numtype)
                               ->where('numresort', $selectedResort->numresort)
                               ->first();

                    $prixBase = $tarif ? $tarif->prix : $this->calculateStandardPrice($tc->numtype, $selectedResort->nbtridents);
                    $prixPromo = $tarif ? $tarif->prix_promo : null;

                    $stats[$p->numperiode][$tc->numtype] = [
                        'valide_pour_resort' => true,
                        'exists_in_db' => ($tarif !== null),
                        'prix_base' => $prixBase,
                        'current_promo' => $prixPromo,
                        'isActive' => ($prixPromo !== null && $prixPromo < $prixBase),
                        'taux_calcule' => ($prixPromo && $prixBase > 0) ? round(($prixPromo / $prixBase) * 100) : 100
                    ];
                }
            }
        }

        return view('marketing.dashboard', compact('resorts', 'selectedResort', 'periodes', 'stats', 'typesChambre', 'typeClubs'));
    }

    // --- MISE À JOUR UNITAIRE BLINDÉE ---
    public function updatePrice(Request $request)
    {
        $request->validate([
            'numperiode' => 'required|integer',
            'numtype' => 'required|integer',
            'numresort' => 'required|integer',
            'valeur' => 'nullable|numeric|min:0',
            'mode' => 'required|in:percentage,amount',
        ]);

        $resort = Resort::find($request->numresort);
        $prixStandard = $this->calculateStandardPrice($request->numtype, $resort->nbtridents);

        $existingTarif = DB::table('tarifer')
            ->where('numperiode', $request->numperiode)
            ->where('numtype', $request->numtype)
            ->where('numresort', $request->numresort)
            ->first();

        $basePriceToUse = $existingTarif ? $existingTarif->prix : $prixStandard;

        $nouveauPrixPromo = null;

        $isCancelled = ($request->valeur === null || 
                       ($request->mode == 'percentage' && $request->valeur >= 100) || 
                       ($request->mode == 'amount' && $request->valeur == 0));

        if (!$isCancelled) {
            if ($request->mode == 'percentage') {
                // Saisie: 80 pour 80% du prix (soit -20%)
                $nouveauPrixPromo = round($basePriceToUse * ($request->valeur / 100), 0);
            } else {
                $nouveauPrixPromo = $request->valeur;
            }

            // Sécurité : Si promo plus chère que base, on annule
            if ($nouveauPrixPromo >= $basePriceToUse) {
                $nouveauPrixPromo = null; 
            }
        }

        // 3. Application en Base (Insert ou Update strict)
        if ($existingTarif) {
            // Update strict
            DB::table('tarifer')
                ->where('numperiode', $request->numperiode)
                ->where('numtype', $request->numtype)
                ->where('numresort', $request->numresort)
                ->update(['prix_promo' => $nouveauPrixPromo]);
        } else {
            // Insert si n'existe pas
            DB::table('tarifer')->insert([
                'numperiode' => $request->numperiode,
                'numtype' => $request->numtype,
                'numresort' => $request->numresort,
                'prix' => $basePriceToUse,
                'prix_promo' => $nouveauPrixPromo
            ]);
        }

        if ($nouveauPrixPromo) {
            return back()->with('success', "Prix mis à jour : **{$nouveauPrixPromo}€**");
        } else {
            return back()->with('success', "Promo annulée ou invalide (retour au tarif standard).");
        }
    }

    // --- MISE À JOUR DE MASSE ---
    public function applyBulkPromo(Request $request)
    {
        // 1. On augmente le temps d'exécution (0 = illimité) pour éviter le crash
        set_time_limit(0);

        $request->validate([
            'numperiode' => 'required|integer',
            'target_type' => 'required|in:global,category',
            'type_club_id' => 'required_if:target_type,category',
            'pourcentage' => 'required|numeric|min:0|max:100',
        ]);

        $periode = Periode::find($request->numperiode);
        
        // Identifier les resorts ciblés
        $query = Resort::query();
        if ($request->target_type === 'category') {
            $query->whereHas('typeClubs', function($q) use ($request) {
                $q->where('typeclub.numtypeclub', $request->type_club_id);
            });
        }
        // On charge les relations pour éviter des requêtes dans la boucle
        $resorts = $query->get();

        $count = 0;
        $pourcentage = $request->pourcentage;

        // 2. On ouvre une TRANSACTION. Tout ce qui se passe ici est groupé.
        DB::transaction(function () use ($resorts, $periode, $pourcentage, &$count) {
            
            foreach ($resorts as $resort) {
                // Récupération des types proposés (Optimisation possible ici mais on garde simple)
                $typesProposes = DB::table('proposer')
                                   ->where('numresort', $resort->numresort)
                                   ->pluck('numtype')
                                   ->toArray();

                foreach ($typesProposes as $idType) {
                    // Calcul prix standard théorique (Logique PHP)
                    $prixStandard = $this->calculateStandardPrice($idType, $resort->nbtridents);

                    // On utilise updateOrInsert ou une logique manuelle, mais on reste dans la transaction
                    
                    // A. On cherche l'existant
                    $existing = DB::table('tarifer')
                        ->where('numperiode', $periode->numperiode)
                        ->where('numtype', $idType)
                        ->where('numresort', $resort->numresort)
                        ->first();

                    // B. On détermine le prix de base
                    $basePrice = $existing ? $existing->prix : $prixStandard;
                    
                    // C. Calcul du prix promo
                    $nouveauPrix = round($basePrice * ($pourcentage / 100), 0);
                    
                    // Sécurité : la promo doit être inférieure au prix
                    if ($nouveauPrix >= $basePrice) {
                        continue; // On ne fait rien si la promo n'est pas avantageuse
                    }

                    // D. Sauvegarde (Insert ou Update)
                    if ($existing) {
                        DB::table('tarifer')
                            ->where('numperiode', $periode->numperiode)
                            ->where('numtype', $idType)
                            ->where('numresort', $resort->numresort)
                            ->update(['prix_promo' => $nouveauPrix]);
                    } else {
                        DB::table('tarifer')->insert([
                            'numperiode' => $periode->numperiode,
                            'numtype' => $idType,
                            'numresort' => $resort->numresort,
                            'prix' => $basePrice,
                            'prix_promo' => $nouveauPrix
                        ]);
                    }
                    $count++;
                }
            }
        });

        return back()->with('success', "Mise à jour terminée ! **{$count}** tarifs ont été mis à jour.");
    }

    public function storePeriode(Request $request)
    {
        $request->validate([
            'nomperiode' => 'required|string|max:100',
            'datedebutperiode' => 'required|date',
            'datefinperiode' => 'required|date|after:datedebutperiode',
        ]);
        Periode::create($request->all());
        return back()->with('success', "Période créée.");
    }

    private function calculateStandardPrice($numType, $nbTridents)
    {
        $base = 250;
        switch ($numType) {
            case 1: $base = 200; break;
            case 2: $base = 300; break;
            case 3: $base = 500; break;
        }
        return $base + ($nbTridents * 50);
    }

    public function resetPromos(Request $request)
    {
        $request->validate([
            'numperiode' => 'required|integer',
        ]);

        $periode = Periode::find($request->numperiode);

        $affected = DB::table('tarifer')
            ->where('numperiode', $request->numperiode)
            ->update(['prix_promo' => null]);

        return back()->with('success', "Réinitialisation réussie ! **{$affected}** promotions supprimées pour la période **{$periode->nomperiode}**.");
    }
}
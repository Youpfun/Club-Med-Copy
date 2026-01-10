<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tarifer;
use App\Models\Periode;
use App\Models\TypeChambre;
use App\Models\Resort;
use App\Models\TypeClub;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class MarketingController extends Controller
{
    /**
     * Affiche le tableau de bord marketing.
     */
    public function index(Request $request)
    {
        $userRole = Auth::user()->role;
        $isMarketing = str_contains(strtolower($userRole), 'marketing');
        $isDirecteur = ($userRole === 'Directeur du Service Marketing');

        if (!$isMarketing) {
            abort(403, "Accès réservé au service marketing.");
        }

        $periodes = Periode::orderBy('datedebutperiode')->get();
        $typeClubs = TypeClub::all();
        $typesChambre = TypeChambre::all(); 

        $resortsList = Resort::with(['pays'])
                             ->withCount('typechambres') 
                             ->orderBy('nomresort')
                             ->get();

        $resorts = collect(); 
        $selectedResort = null;
        $stats = [];

        if ($isDirecteur) {
            $query = Resort::query();
            
            if ($request->filled('type_club')) {
                $query->whereHas('typeClubs', function($q) use ($request) {
                    $q->where('typeclub.numtypeclub', $request->type_club);
                });
            }
            $resorts = $query->orderBy('nomresort')->get();

            $selectedResortId = $request->input('numresort');
            $selectedResort = $selectedResortId ? Resort::find($selectedResortId) : null;

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
        }

        return view('marketing.dashboard', compact(
            'resorts', 
            'selectedResort', 
            'periodes', 
            'stats', 
            'typesChambre', 
            'typeClubs', 
            'resortsList', 
            'isDirecteur'
        ));
    }

    /**
     * Valider définitivement un séjour pour le rendre public (Directeur uniquement).
     */
    public function validateResort($id)
    {
        if (Auth::user()->role !== 'Directeur du Service Marketing') {
            return back()->with('error', "Action non autorisée.");
        }

        $resort = Resort::findOrFail($id);

        $hasPrices = DB::table('tarifer')->where('numresort', $id)->exists();

        if (!$hasPrices) {
            return back()->with('error', "Impossible de valider : Veuillez définir les prix à l'étape 4 avant de mettre en ligne.");
        }

        if (Schema::hasColumn('resort', 'est_valide')) {
            $resort->est_valide = true;
            $resort->save();
            return back()->with('success', "Le séjour **{$resort->nomresort}** est officiellement validé et en ligne ! 🌍");
        } else {
            return back()->with('success', "Le séjour **{$resort->nomresort}** est prêt (Prix détectés).");
        }
    }

    /**
     * Mise à jour unitaire d'un prix (via la grille).
     */
    public function updatePrice(Request $request)
    {
        if (Auth::user()->role !== 'Directeur du Service Marketing') {
            abort(403);
        }

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
                $nouveauPrixPromo = round($basePriceToUse * ($request->valeur / 100), 0);
            } else {
                $nouveauPrixPromo = $request->valeur;
            }

            if ($nouveauPrixPromo >= $basePriceToUse) {
                $nouveauPrixPromo = null; 
            }
        }

        if ($existingTarif) {
            DB::table('tarifer')
                ->where('numperiode', $request->numperiode)
                ->where('numtype', $request->numtype)
                ->where('numresort', $request->numresort)
                ->update(['prix_promo' => $nouveauPrixPromo]);
        } else {
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

    /**
     * Application d'une promotion de masse.
     */
    public function applyBulkPromo(Request $request)
    {
        if (Auth::user()->role !== 'Directeur du Service Marketing') {
            abort(403);
        }

        set_time_limit(0);

        $request->validate([
            'numperiode' => 'required|integer',
            'target_type' => 'required|in:global,category',
            'type_club_id' => 'required_if:target_type,category',
            'pourcentage' => 'required|numeric|min:0|max:100',
        ]);

        $periode = Periode::find($request->numperiode);
        
        $query = Resort::query();
        if ($request->target_type === 'category') {
            $query->whereHas('typeClubs', function($q) use ($request) {
                $q->where('typeclub.numtypeclub', $request->type_club_id);
            });
        }
        $resorts = $query->get();

        $count = 0;
        $pourcentage = $request->pourcentage;

        DB::transaction(function () use ($resorts, $periode, $pourcentage, &$count) {
            foreach ($resorts as $resort) {
                $typesProposes = DB::table('proposer')
                                   ->where('numresort', $resort->numresort)
                                   ->pluck('numtype')
                                   ->toArray();

                foreach ($typesProposes as $idType) {
                    $prixStandard = $this->calculateStandardPrice($idType, $resort->nbtridents);
                    
                    $existing = DB::table('tarifer')
                        ->where('numperiode', $periode->numperiode)
                        ->where('numtype', $idType)
                        ->where('numresort', $resort->numresort)
                        ->first();

                    $basePrice = $existing ? $existing->prix : $prixStandard;
                    $nouveauPrix = round($basePrice * ($pourcentage / 100), 0);
                    
                    if ($nouveauPrix >= $basePrice) continue;

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

        return back()->with('success', "Mise à jour terminée ! **{$count}** tarifs ont été modifiés.");
    }

    public function storePeriode(Request $request)
    {
        if (Auth::user()->role !== 'Directeur du Service Marketing') {
            abort(403);
        }

        $request->validate([
            'nomperiode' => 'required|string|max:100',
            'datedebutperiode' => 'required|date',
            'datefinperiode' => 'required|date|after:datedebutperiode',
        ]);
        Periode::create($request->all());
        return back()->with('success', "Période créée avec succès.");
    }

    public function resetPromos(Request $request)
    {
        if (Auth::user()->role !== 'Directeur du Service Marketing') {
            abort(403);
        }

        $request->validate([
            'numperiode' => 'required|integer',
        ]);

        $periode = Periode::find($request->numperiode);

        $affected = DB::table('tarifer')
            ->where('numperiode', $request->numperiode)
            ->update(['prix_promo' => null]);

        return back()->with('success', "Réinitialisation réussie ! **{$affected}** promotions supprimées pour la période **{$periode->nomperiode}**.");
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
}
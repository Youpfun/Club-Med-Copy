<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tarifer;
use App\Models\Periode;
use App\Models\TypeChambre; // Renommez votre modèle si nécessaire, mais ici on le garde
use Illuminate\Support\Facades\DB;

class MarketingController extends Controller
{
    public function index(Request $request)
    {
        $periodes = Periode::orderBy('datedebutperiode')->get();
        // Le modèle TypeChambre doit correspondre à votre table des types de chambres (avec la colonne 'numtype' ou 'numtypch' selon le modèle)
        $typesChambre = TypeChambre::all(); 

        // La stat doit maintenant être au niveau PERIODE + TYPE CHAMBRE
        $stats = [];
        foreach ($periodes as $p) {
            foreach ($typesChambre as $tc) {
                // CORRECTION : Utilisation de 'numtype' au lieu de 'numtypch' dans le where
                $tarifs = Tarifer::where('numperiode', $p->numperiode)
                                 ->where('numtype', $tc->numtypch) // Assurez-vous que $tc->numtypch est la bonne clé primaire du modèle TypeChambre
                                 ->get();
                
                $total = $tarifs->count();
                $promos = $tarifs->whereNotNull('prix_promo')->count();

                $stats[$p->numperiode][$tc->numtypch] = [
                    'total' => $total,
                    'promos' => $promos,
                    'isActive' => ($total > 0 && $promos > 0),
                    // Taux appliqué, on le calcule à partir du premier tarif en promo
                    'current_taux' => ($promos > 0) 
                        ? round(($tarifs->whereNotNull('prix_promo')->first()->prix_promo / $tarifs->whereNotNull('prix_promo')->first()->prix) * 100) 
                        : 100, // 100% si pas de promo
                ];
            }
        }

        return view('marketing.dashboard', compact('periodes', 'stats', 'typesChambre'));
    }

    public function updatePrice(Request $request)
    {
        // Validation : Taux (le pourcentage que représente le prix final par rapport au prix standard)
        $request->validate([
            'numperiode' => 'required|integer',
            'numtypch' => 'required|integer', // Le nom de l'input HTTP dans la vue est 'numtypch', on le garde ici
            'taux_final' => 'nullable|numeric|min:0|max:100', 
        ]);

        $periode = Periode::find($request->numperiode);
        $typeChambre = TypeChambre::find($request->numtypch); // On trouve le type de chambre via l'ID passé par l'input

        // CORRECTION : Utilisation de 'numtype' dans la requête de Tarifer
        $tarifs = Tarifer::where('numperiode', $request->numperiode)
                         ->where('numtype', $request->numtypch) // CORRECTION : Utilisation de 'numtype'
                         ->whereNotNull('prix')
                         ->get();

        if ($tarifs->isEmpty()) {
            return back()->with('error', "Aucun tarif trouvé pour **{$typeChambre->nomtypch}** dans la période **{$periode->nomperiode}**.");
        }

        // CAS 1 : ANNULATION (Si vide ou 100%, ou 0)
        if ($request->taux_final == null || $request->taux_final >= 100 || $request->taux_final == 0) {
            Tarifer::where('numperiode', $request->numperiode)
                   ->where('numtype', $request->numtypch) // CORRECTION : Utilisation de 'numtype'
                   ->update(['prix_promo' => null]);

            return back()->with('success', "Toutes les promotions ont été supprimées pour **{$typeChambre->nomtypch}** dans **{$periode->nomperiode}**.");
        }

        // CAS 2 : CALCUL DU TAUX FINAL SÉCURISÉ
        $count = 0;
        $ignored = 0;
        
        $tauxFinalDecimal = abs($request->taux_final) / 100; // Ex: 80 devient 0.80

        foreach ($tarifs as $tarif) {
            
            // CALCUL : Prix Final = Prix de Base * Taux Saisi (X%)
            $nouveauPrix = $tarif->prix * $tauxFinalDecimal;
            $nouveauPrix = round($nouveauPrix, 0); 

            // SÉCURITÉ ABSOLUE : Vérifie que le prix est STRICTEMENT inférieur au prix de base
            if ($nouveauPrix < $tarif->prix && $nouveauPrix > 0) {
                $tarif->update(['prix_promo' => $nouveauPrix]);
                $count++;
            } else {
                $tarif->update(['prix_promo' => null]);
                $ignored++;
            }
        }

        $reductionPourcent = 100 - abs($request->taux_final);
        $msg = "Prix mis à jour à **{$request->taux_final}%** du prix initial (soit -{$reductionPourcent}%) pour **{$typeChambre->nomtypch}** ({$count} tarifs modifiés).";
        if ($ignored > 0) {
             $msg .= " ({$ignored} tarifs ignorés ou la réduction est trop faible.)";
        }

        return back()->with('success', $msg);
    }
}
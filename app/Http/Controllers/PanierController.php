<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resort;

class PanierController extends Controller
{
    /**
     * Ajouter un resort au panier (stocké en session).
     */
    public function add(Request $request, $numresort)
    {
        $resort = Resort::findOrFail($numresort);

        $cart = $request->session()->get('cart', []);

        // On évite les doublons : une entrée par resort
        $cart[$numresort] = [
            'id'          => $resort->numresort,
            'nom'         => $resort->nomresort,
            'pays'        => optional($resort->pays)->nompays,
            'nb_chambres' => $resort->nbchambrestotal,
        ];

        $request->session()->put('cart', $cart);

        // Après réservation (utilisateur déjà connecté), on l'emmène
        // directement sur la page "Mes réservations" pour visualiser.
        return redirect()
            ->route('cart.index')
            ->with('success', 'Le resort a été ajouté à vos réservations.');
    }

    /**
     * Afficher les réservations (panier) de l'utilisateur.
     */
    public function index(Request $request)
    {
        $cart = $request->session()->get('cart', []);

        return view('reservations', [
            'reservations' => $cart,
        ]);
    }
}



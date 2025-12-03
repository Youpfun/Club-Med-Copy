<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resort;

class PanierController extends Controller
{
    public function add(Request $request, $numresort)
    {
        $resort = Resort::findOrFail($numresort);

        $cart = $request->session()->get('cart', []);

        $cart[$numresort] = [
            'id'          => $resort->numresort,
            'nom'         => $resort->nomresort,
            'pays'        => optional($resort->pays)->nompays,
            'nb_chambres' => $resort->nbchambrestotal,
        ];

        $request->session()->put('cart', $cart);

        return redirect()
            ->route('cart.index')
            ->with('success', 'Le resort a été ajouté à vos réservations.');
    }
    public function index(Request $request)
    {
        $cart = $request->session()->get('cart', []);

        return view('panier', [
            'reservations' => $cart,
        ]);
    }

    public function remove($numresort)
{
    $cart = session()->get('cart', []);

    if(isset($cart[$numresort])) {
        unset($cart[$numresort]);
        session()->put('cart', $cart);
    }

    return back()->with('success', 'Le resort a été retiré du panier.');
}
}



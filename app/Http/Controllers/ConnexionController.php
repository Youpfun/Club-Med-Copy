<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConnexionController extends Controller
{
    public function show(Request $request)
    {
        // Si on arrive sur la page de connexion après avoir cliqué sur "Réserver",
        // on mémorise le resort à réserver dans la session.
        if ($request->has('reserve_resort')) {
            $request->session()->put('intended_resort', $request->query('reserve_resort'));
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Si l'utilisateur venait pour réserver un resort, on l'ajoute à ses réservations
            // puis on l'envoie directement sur la page "Mes réservations".
            if ($request->session()->has('intended_resort')) {
                $numresort = $request->session()->pull('intended_resort');

                // Ajout au "panier" (réservations) dans la session
                $resort = \App\Models\Resort::find($numresort);
                if ($resort) {
                    $cart = $request->session()->get('cart', []);
                    $cart[$numresort] = [
                        'id'          => $resort->numresort,
                        'nom'         => $resort->nomresort,
                        'pays'        => optional($resort->pays)->nompays,
                        'nb_chambres' => $resort->nbchambrestotal,
                    ];
                    $request->session()->put('cart', $cart);
                }

                return redirect()->route('cart.index')
                    ->with('success', 'Vous êtes connecté et le resort a été ajouté à vos réservations.');
            }

            // Cas classique : connexion sans intention de réservation
            return redirect()->intended('/')->with('success', 'Vous êtes connecté !');
        }

        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ])->onlyInput('email');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Resort;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{
    public function index()
    {
        if (!session()->has('user_2fa_id')) {
            return redirect()->route('login');
        }
        return view('auth.2fa_verify');
    }

    public function store(Request $request)
    {
        $request->validate(['two_factor_code' => 'required']);

        $userId = session()->get('user_2fa_id');
        $user = User::find($userId);

        if (!$user) return redirect()->route('login');

        if ($request->two_factor_code == $user->two_factor_code && 
            $user->two_factor_expires_at->gt(now())) 
        {
            $user->resetTwoFactorCode();
            
            Auth::login($user);
            
            session()->forget('user_2fa_id');

            if (session()->has('intended_resort')) {
                $numresort = session()->pull('intended_resort');
                $resort = Resort::find($numresort);

                if ($resort) {
                    $cart = session()->get('cart', []);
                    $cart[$numresort] = [
                        'id' => $resort->numresort,
                        'nom' => $resort->nomresort,
                        'pays' => optional($resort->pays)->nompays,
                        'nb_chambres' => $resort->nbchambrestotal,
                    ];
                    session()->put('cart', $cart);
                    return redirect()->route('cart.index')->with('success', 'Authentification réussie !');
                }
            }

            return redirect('/')->with('success', 'Bienvenue !');
        }

        return back()->withErrors(['two_factor_code' => 'Code invalide ou expiré.']);
    }

    public function resend()
    {
        $user = User::find(session('user_2fa_id'));
        if ($user) {
            $user->sendTwoFactorCode();
            return back()->with('message', 'Email renvoyé !');
        }
        return redirect()->route('login');
    }
}
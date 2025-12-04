<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ConnexionController extends Controller
{
    public function show(Request $request)
    {
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

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'email' => 'Identifiants incorrects.',
            ])->onlyInput('email');
        }

        $user->sendTwoFactorCode();

        $request->session()->put('user_2fa_id', $user->id);
        
        return redirect()->route('2fa.verify');
    }
}
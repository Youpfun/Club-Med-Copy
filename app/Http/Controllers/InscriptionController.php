<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class InscriptionController extends Controller
{
    public function create()
    {
        return view('auth.inscription');
    }

    public function store(Request $request)
    {
        $request->validate([
            'genre' => 'required|in:M,F',
            'nom' => 'required|string|max:50',
            'prenom' => 'required|string|max:50',
            'datenaissance' => 'required|date|before:today',
            'email' => 'required|email|max:100|unique:users,email',
            'telephone' => 'required|string|max:10', 
            'numrue' => 'required|integer',
            'nomrue' => 'required|string|max:100',
            'codepostal' => 'required|string|max:6',
            'ville' => 'required|string|max:100',
            'password' => 'required|confirmed|min:8',
        ]);

        $user = User::create([
            'genre' => $request->genre,
            'name'  => $request->prenom . ' ' . $request->nom, 
            'datenaissance' => $request->datenaissance,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'numrue' => $request->numrue,
            'nomrue' => $request->nomrue,
            'codepostal' => $request->codepostal,
            'ville' => $request->ville,
            'password' => Hash::make($request->password),
            'role' => 'Utilisateur',
        ]);

        Auth::login($user);

        return redirect('/')->with('success', 'Votre compte a été créé avec succès !');
    }
}
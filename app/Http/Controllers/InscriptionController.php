<?php

namespace App\Http\Controllers;

use App\Models\Client;
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
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'datenaissance' => 'required|date|before:today',
            'email' => 'required|email|max:100|unique:client,emailclient',
            'telephone' => 'required|string|max:15',
            'numrue' => 'required|integer',
            'nomrue' => 'required|string|max:100',
            'codepostal' => 'required|string|max:6',
            'ville' => 'required|string|max:100',
            'password' => 'required|confirmed|min:8',
        ]);

        $client = Client::create([
            'genreclient' => $request->genre,
            'nomclient' => $request->nom,
            'prenomclient' => $request->prenom,
            'datenaissance' => $request->datenaissance,
            'emailclient' => $request->email,
            'telephone' => $request->telephone,
            'numrue' => $request->numrue,
            'nomrue' => $request->nomrue,
            'codepostal' => $request->codepostal,
            'ville' => $request->ville,
            'login' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($client);

        return redirect('/');
    }
}
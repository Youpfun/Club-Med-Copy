<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class InscriptionController extends Controller
{
    public function create()
    {
        return view('auth.inscription');
    }

    public function store(Request $request)
    {
        $rules = [
            'genre' => 'required|in:M,F',
            'nom' => 'required|string|max:50',
            'prenom' => 'required|string|max:50',
            'datenaissance' => 'required|date|before:today|after_or_equal:-120 years',
            'email' => 'required|email|max:100|unique:users,email',
            'telephone' => 'required|string|max:10', 
            'numrue' => 'required|integer',
            'nomrue' => 'required|string|max:100',
            'codepostal' => 'required|string|max:6',
            'ville' => 'required|string|max:100',
            'password' => 'required|confirmed|min:8',
        ];

        $messages = [
            'required' => 'Le champ :attribute est obligatoire.',
            'email' => 'L\'adresse email doit être valide.',
            'unique' => 'Cette adresse email est déjà utilisée.',
            'confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'min' => 'Le mot de passe doit contenir au moins :min caractères.',
            'max' => 'Le champ :attribute ne peut pas dépasser :max caractères.',
            'integer' => 'Le champ :attribute doit être un nombre entier.',
            'before' => 'La date de naissance doit être antérieure à aujourd\'hui.',
            'datenaissance.after_or_equal' => 'L\'âge maximum accepté est de 120 ans.',
        ];

        $attributes = [
            'datenaissance' => 'date de naissance',
            'prenom' => 'prénom',
            'nom' => 'nom',
            'email' => 'adresse email',
            'telephone' => 'téléphone',
            'codepostal' => 'code postal',
            'nomrue' => 'nom de rue',
            'numrue' => 'numéro de rue',
            'password' => 'mot de passe',
        ];

        $request->validate($rules, $messages, $attributes);

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
            'two_factor_preference' => 'email',
        ]);

        $user->sendTwoFactorCode();

        $request->session()->put('user_2fa_id', $user->id);
        
        return redirect()->route('2fa.verify');
    }
}
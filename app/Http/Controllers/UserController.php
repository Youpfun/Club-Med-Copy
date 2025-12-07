<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();

        return view('users', ['users' => $users]);
    }

    public function updateCustom(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'prenom' => 'required|string|max:50',
            'nom' => 'required|string|max:50',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'telephone' => 'nullable|string|max:20',
            'datenaissance' => 'nullable|date',
            'numrue' => 'nullable|integer',
            'nomrue' => 'nullable|string|max:150',
            'codepostal' => 'nullable|string|max:10',
            'ville' => 'nullable|string|max:100',
            'photo' => 'nullable|image|max:1024',
        ]);

        if ($request->hasFile('photo')) {
            $user->updateProfilePhoto($request->file('photo'));
        }

        $fullName = $request->prenom . ' ' . $request->nom;

        $user->forceFill([
            'name' => $fullName,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'datenaissance' => $request->datenaissance,
            'numrue' => $request->numrue,
            'nomrue' => $request->nomrue,
            'codepostal' => $request->codepostal,
            'ville' => $request->ville,
        ])->save();

        return back()->with('success', 'Vos informations ont été mises à jour avec succès.');
    }
}
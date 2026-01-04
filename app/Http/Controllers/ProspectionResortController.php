<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\ProspectionResort;
use App\Mail\ProspectionResortMail;

class ProspectionResortController extends Controller
{
    /**
     * Liste des prospections
     */
    public function index()
    {
        $prospections = ProspectionResort::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('marketing.prospection.index', compact('prospections'));
    }

    /**
     * Formulaire de création d'une prospection
     */
    public function create()
    {
        return view('marketing.prospection.create');
    }

    /**
     * Enregistrer et envoyer une prospection
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom_resort' => 'required|string|max:255',
            'email_resort' => 'required|email|max:255',
            'pays' => 'nullable|string|max:100',
            'ville' => 'nullable|string|max:100',
            'telephone' => 'nullable|string|max:50',
            'objet' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        // Email fixe pour le développement - utiliser l'email du resort saisi
        // En prod, utiliser : $request->email_resort
        $emailDestination = $request->email_resort;

        try {
            $prospection = ProspectionResort::create([
                'user_id' => Auth::id(),
                'nom_resort' => $request->nom_resort,
                'email_resort' => $request->email_resort,
                'pays' => $request->pays,
                'ville' => $request->ville,
                'telephone' => $request->telephone,
                'objet' => $request->objet,
                'message' => $request->message,
                'statut' => 'envoyee',
            ]);

            // Envoi du mail
            Mail::to($emailDestination)->send(
                new ProspectionResortMail($prospection)
            );

            \Log::info('Prospection resort envoyée', [
                'numprospection' => $prospection->numprospection,
                'nom_resort' => $prospection->nom_resort,
                'email_resort' => $prospection->email_resort,
                'email_destination' => $emailDestination,
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('marketing.prospection.index')
                ->with('success', "Email de prospection envoyé à {$prospection->nom_resort} !");

        } catch (\Exception $e) {
            \Log::error('Erreur envoi prospection resort: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Erreur lors de l\'envoi: ' . $e->getMessage());
        }
    }

    /**
     * Voir les détails d'une prospection
     */
    public function show($numprospection)
    {
        $prospection = ProspectionResort::with('user')->findOrFail($numprospection);
        return view('marketing.prospection.show', compact('prospection'));
    }

    /**
     * Mettre à jour le statut d'une prospection
     */
    public function updateStatut(Request $request, $numprospection)
    {
        $request->validate([
            'statut' => 'required|in:envoyee,repondue,en_cours,cloturee',
            'reponse' => 'nullable|string|max:5000',
        ]);

        $prospection = ProspectionResort::findOrFail($numprospection);
        
        $prospection->update([
            'statut' => $request->statut,
            'reponse' => $request->reponse,
            'date_reponse' => $request->statut === 'repondue' ? now() : $prospection->date_reponse,
        ]);

        return redirect()->route('marketing.prospection.show', $numprospection)
            ->with('success', 'Statut mis à jour avec succès.');
    }

    /**
     * Renvoyer l'email
     */
    public function resend($numprospection)
    {
        $prospection = ProspectionResort::findOrFail($numprospection);
        $emailDestination = $prospection->email_resort;

        try {
            Mail::to($emailDestination)->send(
                new ProspectionResortMail($prospection)
            );

            return redirect()->route('marketing.prospection.show', $numprospection)
                ->with('success', 'Email renvoyé avec succès !');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors du renvoi: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer une prospection
     */
    public function destroy($numprospection)
    {
        $prospection = ProspectionResort::findOrFail($numprospection);
        $nomResort = $prospection->nom_resort;
        $prospection->delete();

        return redirect()->route('marketing.prospection.index')
            ->with('success', "Prospection pour {$nomResort} supprimée.");
    }
}

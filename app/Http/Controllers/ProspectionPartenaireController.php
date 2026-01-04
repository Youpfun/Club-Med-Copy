<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\ProspectionPartenaire;
use App\Mail\ProspectionPartenaireMail;

class ProspectionPartenaireController extends Controller
{
    /**
     * Liste des prospections partenaires
     */
    public function index()
    {
        $prospections = ProspectionPartenaire::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('marketing.prospection-partenaire.index', compact('prospections'));
    }

    /**
     * Formulaire de création d'une prospection
     */
    public function create()
    {
        $typesActivite = ProspectionPartenaire::getTypesActivite();
        return view('marketing.prospection-partenaire.create', compact('typesActivite'));
    }

    /**
     * Enregistrer et envoyer une prospection
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom_partenaire' => 'required|string|max:255',
            'email_partenaire' => 'required|email|max:255',
            'type_activite' => 'nullable|string|max:100',
            'pays' => 'nullable|string|max:100',
            'ville' => 'nullable|string|max:100',
            'telephone' => 'nullable|string|max:50',
            'site_web' => 'nullable|url|max:255',
            'objet' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        // Utiliser l'email du partenaire saisi
        $emailDestination = $request->email_partenaire;

        try {
            $prospection = ProspectionPartenaire::create([
                'user_id' => Auth::id(),
                'nom_partenaire' => $request->nom_partenaire,
                'email_partenaire' => $request->email_partenaire,
                'type_activite' => $request->type_activite,
                'pays' => $request->pays,
                'ville' => $request->ville,
                'telephone' => $request->telephone,
                'site_web' => $request->site_web,
                'objet' => $request->objet,
                'message' => $request->message,
                'statut' => 'envoyee',
            ]);

            // Envoi du mail
            Mail::to($emailDestination)->send(
                new ProspectionPartenaireMail($prospection)
            );

            \Log::info('Prospection partenaire envoyée', [
                'numprospection' => $prospection->numprospection,
                'nom_partenaire' => $prospection->nom_partenaire,
                'email_partenaire' => $prospection->email_partenaire,
                'type_activite' => $prospection->type_activite,
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('marketing.prospection-partenaire.index')
                ->with('success', "Email de prospection envoyé à {$prospection->nom_partenaire} !");

        } catch (\Exception $e) {
            \Log::error('Erreur envoi prospection partenaire: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Erreur lors de l\'envoi: ' . $e->getMessage());
        }
    }

    /**
     * Voir les détails d'une prospection
     */
    public function show($numprospection)
    {
        $prospection = ProspectionPartenaire::with('user')->findOrFail($numprospection);
        return view('marketing.prospection-partenaire.show', compact('prospection'));
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

        $prospection = ProspectionPartenaire::findOrFail($numprospection);
        
        $prospection->update([
            'statut' => $request->statut,
            'reponse' => $request->reponse,
            'date_reponse' => $request->statut === 'repondue' ? now() : $prospection->date_reponse,
        ]);

        return redirect()->route('marketing.prospection-partenaire.show', $numprospection)
            ->with('success', 'Statut mis à jour avec succès.');
    }

    /**
     * Renvoyer l'email
     */
    public function resend($numprospection)
    {
        $prospection = ProspectionPartenaire::findOrFail($numprospection);
        $emailDestination = $prospection->email_partenaire;

        try {
            Mail::to($emailDestination)->send(
                new ProspectionPartenaireMail($prospection)
            );

            return redirect()->route('marketing.prospection-partenaire.show', $numprospection)
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
        $prospection = ProspectionPartenaire::findOrFail($numprospection);
        $nomPartenaire = $prospection->nom_partenaire;
        $prospection->delete();

        return redirect()->route('marketing.prospection-partenaire.index')
            ->with('success', "Prospection pour {$nomPartenaire} supprimée.");
    }
}

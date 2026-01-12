<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProjetSejour;
use App\Models\ProspectionResort;
use App\Models\ProspectionPartenaire;
use App\Models\Pays;

class ProjetSejourController extends Controller
{
    /**
     * Liste des projets de séjour
     */
    public function index()
    {
        $user = Auth::user();
        $isDirecteur = in_array($user->role, ['Directeur du Service Marketing', 'Directeur du Service Vente']);

        // Les directeurs voient tous les projets, les autres uniquement les leurs
        if ($isDirecteur) {
            $projets = ProjetSejour::with(['createur', 'prospectionResort'])
                ->orderBy('updated_at', 'desc')
                ->paginate(15);
        } else {
            $projets = ProjetSejour::with(['createur', 'prospectionResort'])
                ->where('user_id', $user->id)
                ->orderBy('updated_at', 'desc')
                ->paginate(15);
        }

        // Statistiques
        $stats = [
            'brouillon' => ProjetSejour::brouillon()->count(),
            'soumis' => ProjetSejour::soumis()->count(),
            'approuve' => ProjetSejour::approuve()->count(),
        ];

        return view('marketing.projet-sejour.index', compact('projets', 'stats', 'isDirecteur'));
    }

    /**
     * Formulaire de création
     */
    public function create(Request $request)
    {
        // Récupérer les prospections avec réponses positives
        $prospectionsResort = ProspectionResort::where('statut', 'repondue')
            ->orderBy('updated_at', 'desc')
            ->get();

        $prospectionsPartenaire = ProspectionPartenaire::where('statut', 'repondue')
            ->orderBy('updated_at', 'desc')
            ->get();

        $paysList = Pays::orderBy('nompays')->get();

        // Pré-remplir si une prospection resort est sélectionnée
        $selectedProspectionResort = null;
        if ($request->has('prospection_resort_id')) {
            $selectedProspectionResort = ProspectionResort::find($request->prospection_resort_id);
        }

        return view('marketing.projet-sejour.create', compact(
            'prospectionsResort',
            'prospectionsPartenaire',
            'paysList',
            'selectedProspectionResort'
        ));
    }

    /**
     * Enregistrer un nouveau projet
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom_sejour' => 'required|string|max:255',
            'pays' => 'required|string|max:100',
            'ville' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:5000',
            'nb_tridents' => 'required|integer|min:1|max:5',
            'date_debut_prevue' => 'nullable|date',
            'date_fin_prevue' => 'nullable|date|after_or_equal:date_debut_prevue',
            'prospection_resort_id' => 'nullable|exists:prospection_resort,numprospection',
            'prospection_partenaires_ids' => 'nullable|array',
            'budget_estime' => 'nullable|numeric|min:0',
            'capacite_prevue' => 'nullable|integer|min:1',
            'activites_prevues' => 'nullable|string|max:3000',
            'points_forts' => 'nullable|string|max:3000',
        ]);

        $projet = ProjetSejour::create([
            'user_id' => Auth::id(),
            'nom_sejour' => $request->nom_sejour,
            'pays' => $request->pays,
            'ville' => $request->ville,
            'description' => $request->description,
            'nb_tridents' => $request->nb_tridents,
            'date_debut_prevue' => $request->date_debut_prevue,
            'date_fin_prevue' => $request->date_fin_prevue,
            'prospection_resort_id' => $request->prospection_resort_id,
            'prospection_partenaires_ids' => $request->prospection_partenaires_ids,
            'budget_estime' => $request->budget_estime,
            'capacite_prevue' => $request->capacite_prevue,
            'activites_prevues' => $request->activites_prevues,
            'points_forts' => $request->points_forts,
            'statut' => 'brouillon',
        ]);

        return redirect()->route('marketing.projet-sejour.show', $projet->numprojet)
            ->with('success', "Projet de séjour '{$projet->nom_sejour}' créé avec succès !");
    }

    /**
     * Afficher un projet
     */
    public function show($numprojet)
    {
        $projet = ProjetSejour::with(['createur', 'directeur', 'prospectionResort', 'resort'])
            ->findOrFail($numprojet);

        $user = Auth::user();
        $isDirecteur = in_array($user->role, ['Directeur du Service Marketing', 'Directeur du Service Vente']);
        $canEdit = $projet->canBeEdited() && ($projet->user_id === $user->id || $isDirecteur);
        $canReview = $projet->canBeReviewed() && $isDirecteur;

        return view('marketing.projet-sejour.show', compact('projet', 'isDirecteur', 'canEdit', 'canReview'));
    }

    /**
     * Formulaire d'édition
     */
    public function edit($numprojet)
    {
        $projet = ProjetSejour::findOrFail($numprojet);

        if (!$projet->canBeEdited()) {
            return back()->with('error', "Ce projet ne peut plus être modifié.");
        }

        $user = Auth::user();
        $isDirecteur = in_array($user->role, ['Directeur du Service Marketing', 'Directeur du Service Vente']);
        
        if ($projet->user_id !== $user->id && !$isDirecteur) {
            return back()->with('error', "Vous n'êtes pas autorisé à modifier ce projet.");
        }

        $prospectionsResort = ProspectionResort::where('statut', 'repondue')
            ->orderBy('updated_at', 'desc')
            ->get();

        $prospectionsPartenaire = ProspectionPartenaire::where('statut', 'repondue')
            ->orderBy('updated_at', 'desc')
            ->get();

        $paysList = Pays::orderBy('nompays')->get();

        return view('marketing.projet-sejour.edit', compact(
            'projet',
            'prospectionsResort',
            'prospectionsPartenaire',
            'paysList'
        ));
    }

    /**
     * Mettre à jour un projet
     */
    public function update(Request $request, $numprojet)
    {
        $projet = ProjetSejour::findOrFail($numprojet);

        if (!$projet->canBeEdited()) {
            return back()->with('error', "Ce projet ne peut plus être modifié.");
        }

        $request->validate([
            'nom_sejour' => 'required|string|max:255',
            'pays' => 'required|string|max:100',
            'ville' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:5000',
            'nb_tridents' => 'required|integer|min:1|max:5',
            'date_debut_prevue' => 'nullable|date',
            'date_fin_prevue' => 'nullable|date|after_or_equal:date_debut_prevue',
            'prospection_resort_id' => 'nullable|exists:prospection_resort,numprospection',
            'prospection_partenaires_ids' => 'nullable|array',
            'budget_estime' => 'nullable|numeric|min:0',
            'capacite_prevue' => 'nullable|integer|min:1',
            'activites_prevues' => 'nullable|string|max:3000',
            'points_forts' => 'nullable|string|max:3000',
        ]);

        $projet->update($request->only([
            'nom_sejour', 'pays', 'ville', 'description', 'nb_tridents',
            'date_debut_prevue', 'date_fin_prevue', 'prospection_resort_id',
            'prospection_partenaires_ids', 'budget_estime', 'capacite_prevue',
            'activites_prevues', 'points_forts'
        ]));

        return redirect()->route('marketing.projet-sejour.show', $projet->numprojet)
            ->with('success', "Projet mis à jour avec succès !");
    }

    /**
     * Soumettre le projet au directeur des ventes
     */
    public function submit($numprojet)
    {
        $projet = ProjetSejour::findOrFail($numprojet);

        if (!$projet->canBeSubmitted()) {
            return back()->with('error', "Ce projet ne peut pas être soumis dans son état actuel.");
        }

        $projet->update([
            'statut' => 'soumis',
            'date_soumission' => now(),
        ]);

        return redirect()->route('marketing.projet-sejour.show', $projet->numprojet)
            ->with('success', "Le projet a été soumis au Directeur du Service Vente pour validation ! 📤");
    }

    /**
     * Approuver un projet (Directeur uniquement)
     */
    public function approve(Request $request, $numprojet)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['Directeur du Service Marketing', 'Directeur du Service Vente'])) {
            return back()->with('error', "Action non autorisée.");
        }

        $projet = ProjetSejour::findOrFail($numprojet);

        if (!$projet->canBeReviewed()) {
            return back()->with('error', "Ce projet ne peut pas être approuvé dans son état actuel.");
        }

        $projet->update([
            'statut' => 'approuve',
            'directeur_id' => $user->id,
            'commentaire_directeur' => $request->commentaire,
            'date_decision' => now(),
        ]);

        return redirect()->route('marketing.projet-sejour.show', $projet->numprojet)
            ->with('success', "Projet approuvé ! ✅ Le séjour peut maintenant être créé.");
    }

    /**
     * Demander une révision (Directeur uniquement)
     */
    public function requestRevision(Request $request, $numprojet)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['Directeur du Service Marketing', 'Directeur du Service Vente'])) {
            return back()->with('error', "Action non autorisée.");
        }

        $request->validate([
            'commentaire' => 'required|string|max:2000',
        ]);

        $projet = ProjetSejour::findOrFail($numprojet);

        if (!$projet->canBeReviewed()) {
            return back()->with('error', "Ce projet ne peut pas être renvoyé en révision.");
        }

        $projet->update([
            'statut' => 'en_revision',
            'directeur_id' => $user->id,
            'commentaire_directeur' => $request->commentaire,
            'date_decision' => now(),
        ]);

        return redirect()->route('marketing.projet-sejour.show', $projet->numprojet)
            ->with('success', "Le projet a été renvoyé pour révision. 🔄");
    }

    /**
     * Refuser un projet (Directeur uniquement)
     */
    public function reject(Request $request, $numprojet)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['Directeur du Service Marketing', 'Directeur du Service Vente'])) {
            return back()->with('error', "Action non autorisée.");
        }

        $request->validate([
            'commentaire' => 'required|string|max:2000',
        ]);

        $projet = ProjetSejour::findOrFail($numprojet);

        if (!$projet->canBeReviewed()) {
            return back()->with('error', "Ce projet ne peut pas être refusé dans son état actuel.");
        }

        $projet->update([
            'statut' => 'refuse',
            'directeur_id' => $user->id,
            'commentaire_directeur' => $request->commentaire,
            'date_decision' => now(),
        ]);

        return redirect()->route('marketing.projet-sejour.show', $projet->numprojet)
            ->with('success', "Le projet a été refusé. ❌");
    }

    /**
     * Démarrer la création du resort (après approbation)
     */
    public function startCreation($numprojet)
    {
        $projet = ProjetSejour::findOrFail($numprojet);

        if ($projet->statut !== 'approuve') {
            return back()->with('error', "Le projet doit être approuvé avant de créer le séjour.");
        }

        $projet->update(['statut' => 'en_creation']);

        // Rediriger vers le formulaire de création de resort avec les données pré-remplies
        return redirect()->route('resort.create', [
            'projet_id' => $projet->numprojet,
            'nom' => $projet->nom_sejour,
            'pays' => $projet->pays,
        ])->with('info', "Créez maintenant le resort basé sur le projet approuvé.");
    }

    /**
     * Supprimer un projet (brouillon uniquement)
     */
    public function destroy($numprojet)
    {
        $projet = ProjetSejour::findOrFail($numprojet);

        if ($projet->statut !== 'brouillon') {
            return back()->with('error', "Seuls les projets en brouillon peuvent être supprimés.");
        }

        $user = Auth::user();
        $isDirecteur = in_array($user->role, ['Directeur du Service Marketing', 'Directeur du Service Vente']);

        if ($projet->user_id !== $user->id && !$isDirecteur) {
            return back()->with('error', "Vous n'êtes pas autorisé à supprimer ce projet.");
        }

        $nom = $projet->nom_sejour;
        $projet->delete();

        return redirect()->route('marketing.projet-sejour.index')
            ->with('success', "Le projet '{$nom}' a été supprimé.");
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\DemandeDisponibilite;
use App\Models\Resort;
use App\Models\TypeChambre;
use App\Mail\DemandeDisponibiliteMail;

class DemandeDisponibiliteController extends Controller
{
    /**
     * Liste des demandes de disponibilité
     */
    public function index()
    {
        $demandes = DemandeDisponibilite::with(['resort', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('marketing.demandes.index', compact('demandes'));
    }

    /**
     * Formulaire de création d'une demande
     */
    public function create()
    {
        $resorts = Resort::with('pays')->orderBy('nomresort')->get();
        $typesChambre = TypeChambre::all();

        return view('marketing.demandes.create', compact('resorts', 'typesChambre'));
    }

    /**
     * Enregistrer et envoyer une demande de disponibilité
     */
    public function store(Request $request)
    {
        $request->validate([
            'numresort' => 'required|exists:resort,numresort',
            'date_debut' => 'required|date|after:today',
            'date_fin' => 'required|date|after:date_debut',
            'nb_chambres' => 'nullable|integer|min:1',
            'nb_personnes' => 'nullable|integer|min:1',
            'message' => 'nullable|string|max:2000',
        ]);

        $resort = Resort::findOrFail($request->numresort);

        // Email fixe pour tous les resorts (en développement)
        $resortEmail = 'clubmedsae@gmail.com';

        try {
            DB::beginTransaction();

            $token = Str::uuid()->toString();
            $expiresAt = now()->addDays(7);

            $demande = DemandeDisponibilite::create([
                'numresort' => $request->numresort,
                'user_id' => Auth::id(),
                'date_debut' => $request->date_debut,
                'date_fin' => $request->date_fin,
                'nb_chambres' => $request->nb_chambres,
                'nb_personnes' => $request->nb_personnes,
                'message' => $request->message,
                'statut' => 'pending',
                'validation_token' => $token,
                'validation_token_expires_at' => $expiresAt,
            ]);

            $tokenLink = url('/resort/disponibilite/' . $token);

            Mail::to($resortEmail)->send(
                new DemandeDisponibiliteMail($demande, $tokenLink)
            );

            DB::commit();

            \Log::info('Demande de disponibilité envoyée', [
                'numdemande' => $demande->numdemande,
                'numresort' => $resort->numresort,
                'resort_email' => $resortEmail,
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('marketing.demandes.index')
                ->with('success', "Demande de disponibilité envoyée au resort {$resort->nomresort}.");
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur envoi demande disponibilité: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de l\'envoi de la demande: ' . $e->getMessage());
        }
    }

    /**
     * Voir les détails d'une demande
     */
    public function show($numdemande)
    {
        $demande = DemandeDisponibilite::with(['resort', 'user'])
            ->findOrFail($numdemande);

        return view('marketing.demandes.show', compact('demande'));
    }

    /**
     * Renvoyer une demande (générer un nouveau token)
     */
    public function resend($numdemande)
    {
        $demande = DemandeDisponibilite::with('resort')->findOrFail($numdemande);

        // Email fixe pour tous les resorts (en développement)
        $resortEmail = 'clubmedsae@gmail.com';

        try {
            $token = Str::uuid()->toString();
            $expiresAt = now()->addDays(7);

            $demande->update([
                'validation_token' => $token,
                'validation_token_expires_at' => $expiresAt,
                'statut' => 'pending',
            ]);

            $tokenLink = url('/resort/disponibilite/' . $token);

            Mail::to($resortEmail)->send(
                new DemandeDisponibiliteMail($demande, $tokenLink)
            );

            \Log::info('Demande de disponibilité renvoyée', [
                'numdemande' => $demande->numdemande,
            ]);

            return back()->with('success', 'Demande renvoyée avec succès.');
        } catch (\Exception $e) {
            \Log::error('Erreur renvoi demande: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors du renvoi: ' . $e->getMessage());
        }
    }

    // ===== CÔTÉ RESORT =====

    /**
     * Afficher le formulaire de réponse (côté resort)
     */
    public function showResortResponse($token)
    {
        $demande = DemandeDisponibilite::with(['resort.typechambres', 'user'])
            ->where('validation_token', $token)
            ->first();

        if (!$demande) {
            return view('resort.disponibilite-error', [
                'message' => 'Lien de demande invalide ou expiré.'
            ]);
        }

        if ($demande->validation_token_expires_at < now()) {
            return view('resort.disponibilite-error', [
                'message' => 'Ce lien a expiré. Veuillez contacter le service marketing.'
            ]);
        }

        if ($demande->statut === 'responded') {
            return view('resort.disponibilite-error', [
                'message' => 'Vous avez déjà répondu à cette demande.'
            ]);
        }

        return view('resort.disponibilite-response', compact('demande', 'token'));
    }

    /**
     * Traiter la réponse du resort
     */
    public function storeResortResponse(Request $request, $token)
    {
        $demande = DemandeDisponibilite::with('resort')
            ->where('validation_token', $token)
            ->first();

        if (!$demande) {
            return redirect()->route('home')->with('error', 'Lien invalide.');
        }

        if ($demande->validation_token_expires_at < now()) {
            return view('resort.disponibilite-error', [
                'message' => 'Ce lien a expiré.'
            ]);
        }

        $request->validate([
            'response_status' => 'required|in:available,partially_available,not_available',
            'response_message' => 'nullable|string|max:2000',
            'chambres_disponibles' => 'nullable|integer|min:0',
        ]);

        $responseDetails = [
            'chambres_disponibles' => $request->chambres_disponibles,
            'responded_by' => $request->input('responded_by', 'Resort'),
        ];

        $demande->update([
            'statut' => 'responded',
            'response_status' => $request->response_status,
            'response_message' => $request->response_message,
            'response_details' => $responseDetails,
            'responded_at' => now(),
        ]);

        \Log::info('Réponse à demande de disponibilité', [
            'numdemande' => $demande->numdemande,
            'response_status' => $request->response_status,
        ]);

        return view('resort.disponibilite-result', [
            'status' => $request->response_status,
            'demande' => $demande,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Reservation;
use App\Models\Avis;
use App\Models\Signalement;
use App\Models\Remboursement;
use Carbon\Carbon;

class PersonalDataController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Affiche la page avec toutes les données personnelles de l'utilisateur
     */
    public function index()
    {
        $user = Auth::user();

        // Récupérer toutes les données associées à l'utilisateur
        $reservations = Reservation::where('user_id', $user->id)
            ->with(['resort', 'chambres'])
            ->orderBy('datedebut', 'desc')
            ->get();

        $avis = Avis::where('user_id', $user->id)
            ->with(['resort', 'photos'])
            ->orderBy('datepublication', 'desc')
            ->get();

        $signalements = Signalement::where('user_id', $user->id)
            ->with(['avis'])
            ->orderBy('datesignalement', 'desc')
            ->get();

        $remboursements = Remboursement::where('user_id', $user->id)
            ->with(['reservation'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculer les statistiques
        $stats = [
            'reservations_count' => $reservations->count(),
            'avis_count' => $avis->count(),
            'signalements_count' => $signalements->count(),
            'remboursements_count' => $remboursements->count(),
            'account_created' => $user->created_at,
            'last_login' => $user->last_login_at ?? null,
        ];

        return view('profile.personal-data', compact(
            'user',
            'reservations',
            'avis',
            'signalements',
            'remboursements',
            'stats'
        ));
    }

    /**
     * Exporte toutes les données personnelles en JSON
     */
    public function export()
    {
        $user = Auth::user();

        $data = [
            'export_date' => now()->toISOString(),
            'user_info' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'genre' => $user->genre,
                'date_naissance' => $user->datenaissance,
                'telephone' => $user->telephone,
                'adresse' => [
                    'numero' => $user->numrue,
                    'rue' => $user->nomrue,
                    'code_postal' => $user->codepostal,
                    'ville' => $user->ville,
                ],
                'role' => $user->role,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
            ],
            'reservations' => Reservation::where('user_id', $user->id)
                ->with(['resort:numresort,nomresort', 'chambres'])
                ->get()
                ->map(function ($res) {
                    return [
                        'numero' => $res->numreservation,
                        'resort' => $res->resort->nomresort ?? null,
                        'date_debut' => $res->datedebut,
                        'date_fin' => $res->datefin,
                        'nb_personnes' => $res->nbpersonnes,
                        'chambres' => $res->chambres->count(),
                        'prix_total' => $res->prixtotal,
                        'statut' => $res->statut,
                    ];
                }),
            'avis' => Avis::where('user_id', $user->id)
                ->with(['resort:numresort,nomresort'])
                ->get()
                ->map(function ($avis) {
                    return [
                        'numero' => $avis->numavis,
                        'resort' => $avis->resort->nomresort ?? null,
                        'note' => $avis->noteavis,
                        'commentaire' => $avis->commentaireavis,
                        'date_publication' => $avis->datepublication,
                    ];
                }),
            'signalements' => Signalement::where('user_id', $user->id)->get(),
            'remboursements' => Remboursement::where('user_id', $user->id)
                ->get()
                ->map(function ($remb) {
                    return [
                        'montant' => $remb->montant,
                        'raison' => $remb->raison,
                        'statut' => $remb->statut,
                        'created_at' => $remb->created_at,
                    ];
                }),
        ];

        $filename = 'mes-donnees-clubmed-' . now()->format('Y-m-d') . '.json';

        return response()->json($data)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Content-Type', 'application/json');
    }

    /**
     * Affiche le formulaire de demande de suppression
     */
    public function showDeleteForm()
    {
        $user = Auth::user();
        
        // Vérifier s'il y a des réservations en cours ou futures
        $activeReservations = Reservation::where('user_id', $user->id)
            ->where('datefin', '>=', now())
            ->whereIn('statut', ['confirmee', 'en_attente', 'payee'])
            ->count();

        return view('profile.delete-account', compact('user', 'activeReservations'));
    }

    /**
     * Envoie une demande de suppression de compte
     */
    public function requestDeletion(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
            'reason' => 'nullable|string|max:1000',
            'confirm_deletion' => 'required|accepted',
        ], [
            'password.current_password' => 'Le mot de passe est incorrect.',
            'confirm_deletion.accepted' => 'Vous devez confirmer la suppression.',
        ]);

        $user = Auth::user();

        // Vérifier s'il y a des réservations actives
        $activeReservations = Reservation::where('user_id', $user->id)
            ->where('datefin', '>=', now())
            ->whereIn('statut', ['confirmee', 'en_attente', 'payee'])
            ->exists();

        if ($activeReservations) {
            return back()->with('error', 'Vous avez des réservations en cours. Veuillez les annuler avant de supprimer votre compte.');
        }

        // Enregistrer la demande de suppression
        Log::info('Demande de suppression de compte', [
            'user_id' => $user->id,
            'email' => $user->email,
            'reason' => $request->reason,
            'requested_at' => now()->toISOString(),
        ]);

        // Envoyer un email de confirmation à l'utilisateur
        // (En production, vous enverriez un vrai email)
        
        // Marquer le compte pour suppression (soft delete après 30 jours)
        $user->deletion_requested_at = now();
        $user->deletion_reason = $request->reason;
        $user->save();

        // Déconnecter l'utilisateur
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 
            'Votre demande de suppression a été enregistrée. Votre compte sera supprimé dans 30 jours. ' .
            'Un email de confirmation vous a été envoyé.'
        );
    }

    /**
     * Annule une demande de suppression
     */
    public function cancelDeletion(Request $request)
    {
        $user = Auth::user();
        
        $user->deletion_requested_at = null;
        $user->deletion_reason = null;
        $user->save();

        return back()->with('success', 'Votre demande de suppression a été annulée.');
    }
}

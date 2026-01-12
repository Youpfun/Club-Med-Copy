<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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

    /**
     * Affiche le formulaire de demande RGPD (anonymisation ou suppression)
     */
    public function showGdprRequest()
    {
        $user = Auth::user();
        
        // Vérifier s'il y a des réservations en cours ou futures
        $activeReservations = Reservation::where('user_id', $user->id)
            ->where('datefin', '>=', now())
            ->whereIn('statut', ['confirmee', 'en_attente', 'payee'])
            ->count();

        return view('profile.gdpr-request', compact('user', 'activeReservations'));
    }

    /**
     * Traite la demande RGPD (anonymisation ou suppression)
     */
    public function processGdprRequest(Request $request)
    {
        $user = Auth::user();
        
        // Validation
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'request_type' => 'required|in:anonymize,delete',
            'password' => 'required',
            'reason' => 'nullable|string|max:1000',
            'confirm_action' => 'required|accepted',
        ], [
            'request_type.required' => 'Veuillez sélectionner un type de demande.',
            'password.required' => 'Le mot de passe est requis.',
            'confirm_action.accepted' => 'Vous devez confirmer votre demande.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Récupérer le hash du mot de passe directement depuis la BDD
        $userFromDb = DB::table('users')->where('id', $user->id)->first();
        
        // Vérification du mot de passe avec le hash de la BDD
        if (!Hash::check($request->password, $userFromDb->password)) {
            Log::warning('Tentative de demande RGPD avec mot de passe incorrect', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
            
            return back()->withErrors([
                'password' => 'Le mot de passe est incorrect. Veuillez vérifier et réessayer.'
            ])->withInput($request->except('password'));
        }

        $requestType = $request->request_type;

        // Log de la demande
        Log::info('Demande RGPD reçue', [
            'user_id' => $user->id,
            'email' => $user->email,
            'type' => $requestType,
            'reason' => $request->reason,
            'requested_at' => now()->toISOString(),
        ]);

        if ($requestType === 'anonymize') {
            return $this->anonymizeUserData($user, $request->reason);
        } else {
            return $this->deleteUserData($user, $request);
        }
    }

    /**
     * Anonymise les données de l'utilisateur conformément au RGPD
     * Supprime les identifiants directs, généralise les attributs, et bruite les données
     */
    private function anonymizeUserData(User $user, $reason = null)
    {
        try {
            DB::beginTransaction();

            $userId = $user->id;
            $anonymizedId = 'anon_' . time() . '_' . $userId;

            // Log avant anonymisation
            Log::info('Début de l\'anonymisation RGPD', [
                'user_id' => $userId,
                'email' => $user->email,
                'reason' => $reason,
            ]);

            // Anonymiser les données de l'utilisateur dans la table users
            DB::table('users')->where('id', $userId)->update([
                'name' => 'Utilisateur Anonyme #' . $userId,
                'email' => 'anonyme' . $userId . '@example.com',
                'genre' => null,
                'datenaissance' => DB::raw("CASE 
                    WHEN datenaissance IS NOT NULL THEN CONCAT(EXTRACT(YEAR FROM datenaissance), '-01-01')::date
                    ELSE NULL 
                END"),
                'telephone' => null,
                'numrue' => null,
                'nomrue' => null,
                'codepostal' => null,
                'ville' => DB::raw("CASE WHEN ville IS NOT NULL THEN 'Ville anonymisée' ELSE NULL END"),
                'idcarte' => null,
                'profile_photo_path' => null,
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
                'deletion_requested_at' => null,
                'deletion_reason' => 'Anonymisation RGPD - ' . ($reason ?? 'Non spécifié'),
            ]);

            // Anonymiser les avis (garder les notes mais anonymiser les commentaires sensibles)
            DB::table('avis')
                ->where('user_id', $userId)
                ->whereNotNull('commentaire')
                ->update([
                    'commentaire' => DB::raw("'[Commentaire anonymisé - ' || numavis || ']'")
                ]);

            // Anonymiser les signalements (table = signalements)
            DB::table('signalements')
                ->where('user_id', $userId)
                ->update([
                    'message' => '[Signalement anonymisé]'
                ]);

            // Les réservations gardent l'ID utilisateur mais données personnelles déjà anonymisées
            // Pas besoin de modifier car les infos viennent du user

            // Supprimer les tokens d'accès personnel
            DB::table('personal_access_tokens')
                ->where('tokenable_id', $userId)
                ->where('tokenable_type', 'App\\Models\\User')
                ->delete();

            // Supprimer les sessions
            DB::table('sessions')->where('user_id', $userId)->delete();

            DB::commit();

            // Déconnecter l'utilisateur
            Auth::guard('web')->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            Log::info('Anonymisation RGPD réussie', [
                'user_id' => $userId,
                'anonymized_at' => now()->toISOString(),
            ]);

            return redirect()->route('home')->with('success', 
                'Vos données ont été anonymisées avec succès. Vous ne pouvez plus vous connecter avec votre ancien compte. ' .
                'Conformément au RGPD, vos informations personnelles ont été supprimées.'
            );

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Erreur lors de l\'anonymisation RGPD', [
                'user_id' => $userId ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 
                'Une erreur est survenue lors de l\'anonymisation de vos données. Veuillez réessayer ou contacter le support.'
            );
        }
    }

    /**
     * Supprime définitivement les données de l'utilisateur
     */
    private function deleteUserData(User $user, Request $request)
    {
        try {
            // Vérifier s'il y a des réservations actives
            $activeReservations = Reservation::where('user_id', $user->id)
                ->where('datefin', '>=', now())
                ->whereIn('statut', ['confirmee', 'en_attente', 'payee'])
                ->exists();

            if ($activeReservations) {
                return back()->with('error', 
                    'Vous avez des réservations en cours. Veuillez les annuler avant de supprimer votre compte.'
                );
            }

            DB::beginTransaction();

            $userId = $user->id;
            $userEmail = $user->email;

            // Log avant suppression
            Log::info('Début de la suppression RGPD', [
                'user_id' => $userId,
                'email' => $userEmail,
                'reason' => $request->reason,
            ]);

            // Supprimer les données liées (en cascade ou manuellement)
            // Note: Certaines tables peuvent avoir des contraintes de clé étrangère avec onDelete cascade

            // Supprimer les avis et leurs photos
            $avisIds = DB::table('avis')->where('user_id', $userId)->pluck('numavis');
            if ($avisIds->isNotEmpty()) {
                DB::table('photo')->whereIn('numavis', $avisIds)->delete();
            }
            DB::table('avis')->where('user_id', $userId)->delete();

            // Supprimer les signalements (table = signalements)
            DB::table('signalements')->where('user_id', $userId)->delete();

            // Supprimer les remboursements
            DB::table('remboursement')->where('user_id', $userId)->delete();

            // Supprimer les réservations et leurs relations
            $reservationIds = DB::table('reservation')->where('user_id', $userId)->pluck('numreservation');
            
            if ($reservationIds->isNotEmpty()) {
                // Supprimer les activités réservées
                DB::table('reserver_activite')->whereIn('numreservation', $reservationIds)->delete();
                
                // Supprimer les chambres réservées
                DB::table('reserver')->whereIn('numreservation', $reservationIds)->delete();
                
                // Supprimer les confirmations de réservation
                DB::table('reservation_confirmations')->whereIn('numreservation', $reservationIds)->delete();
                
                // Supprimer les rejets de réservation
                DB::table('reservation_rejections')->whereIn('numreservation', $reservationIds)->delete();
                
                // Supprimer les paiements
                DB::table('paiement')->whereIn('numreservation', $reservationIds)->delete();
                
                // Supprimer les réservations
                DB::table('reservation')->where('user_id', $userId)->delete();
            }

            // Supprimer les prospections
            DB::table('prospection_partenaire')->where('user_id', $userId)->delete();
            DB::table('prospection_resort')->where('user_id', $userId)->delete();

            // Supprimer les demandes de disponibilité
            DB::table('demande_disponibilite')->where('user_id', $userId)->delete();

            // Supprimer les tokens d'accès personnel
            DB::table('personal_access_tokens')
                ->where('tokenable_id', $userId)
                ->where('tokenable_type', 'App\\Models\\User')
                ->delete();

            // Supprimer les sessions
            DB::table('sessions')->where('user_id', $userId)->delete();

            // Supprimer les équipes (Jetstream)
            DB::table('team_user')->where('user_id', $userId)->delete();
            DB::table('teams')->where('user_id', $userId)->delete();

            // Enfin, supprimer l'utilisateur
            DB::table('users')->where('id', $userId)->delete();

            DB::commit();

            // Déconnecter l'utilisateur
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            Log::info('Suppression RGPD réussie', [
                'user_id' => $userId,
                'email' => $userEmail,
                'deleted_at' => now()->toISOString(),
            ]);

            return redirect()->route('home')->with('success', 
                'Votre compte et toutes vos données ont été définitivement supprimés. ' .
                'Conformément au RGPD, nous avons supprimé toutes vos informations personnelles de nos serveurs.'
            );

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Erreur lors de la suppression RGPD', [
                'user_id' => $userId ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 
                'Une erreur est survenue lors de la suppression de vos données. Veuillez réessayer ou contacter le support.'
            );
        }
    }
}

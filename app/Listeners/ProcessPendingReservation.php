<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Transport;
use Carbon\Carbon;

class ProcessPendingReservation
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $pendingReservation = Session::get('pending_reservation');

        if (!$pendingReservation) {
            return;
        }

        $user = $event->user;
        
        try {
            $numresort = $pendingReservation['numresort'];
            $dateDebut = $pendingReservation['dateDebut'];
            $dateFin = $pendingReservation['dateFin'];
            $chambres = $pendingReservation['chambres'] ?? [];
            $transports = $pendingReservation['transports'] ?? [];
            $nbAdultes = $pendingReservation['nbAdultes'];
            $nbEnfants = $pendingReservation['nbEnfants'];
            $activites = $pendingReservation['activites'] ?? [];
            $participantsData = $pendingReservation['participants'] ?? [];

            $nbNuits = Carbon::parse($dateDebut)->diffInDays(Carbon::parse($dateFin));
            $nbPersonnes = $nbAdultes + $nbEnfants;

            // Calculer le prix des chambres
            $prixChambre = 0;
            foreach ($chambres as $numtype => $qty) {
                if ($qty > 0) {
                    $prixParNuit = $this->getPrixChambre($numtype, $dateDebut);
                    $prixChambre += $prixParNuit * $nbNuits * $qty;
                }
            }

            // Calculer le prix des transports
            $prixTransport = 0;
            $numtransport = null;
            foreach ($transports as $transport) {
                if ($transport) {
                    $transportModel = Transport::find($transport);
                    if ($transportModel) {
                        $prixTransport += $transportModel->prixtransport;
                        $numtransport = $transport;
                    }
                }
            }

            // Calculer le prix des activités
            $prixActivites = 0;
            foreach ($activites as $numactivite => $participants) {
                if (is_array($participants) && count($participants) > 0) {
                    $activite = DB::table('activitealacarte')->where('numactivite', $numactivite)->first();
                    if ($activite) {
                        $prixActivites += $activite->prixmin * count($participants);
                    }
                }
            }

            $sousTotal = $prixChambre + $prixTransport + $prixActivites;
            $tva = $sousTotal * 0.2;
            $prixTotal = $sousTotal + $tva;

            // Créer la réservation
            $numreservation = DB::table('reservation')->insertGetId([
                'user_id' => $user->id,
                'numresort' => $numresort,
                'numjour' => 1,
                'pla_numjour' => 1,
                'numtransport' => $numtransport,
                'statut' => 'En attente',
                'nbpersonnes' => $nbPersonnes,
                'prixtotal' => $prixTotal,
                'datedebut' => $dateDebut,
                'datefin' => $dateFin,
            ], 'numreservation');

            // Ajouter les chambres
            foreach ($chambres as $numtype => $qty) {
                if ($qty > 0) {
                    DB::table('choisir')->insert([
                        'numreservation' => $numreservation,
                        'numtype' => $numtype,
                        'quantite' => $qty,
                    ]);
                }
            }

            // Ajouter les participants
            $participantsMap = [];
            
            for ($i = 1; $i <= $nbAdultes; $i++) {
                $key = 'adulte_' . $i;
                $numtransportParticipant = $transports[$key] ?? null;
                
                $participantInfo = $participantsData[$key] ?? [];
                $nom = $participantInfo['nom'] ?? '';
                $prenom = $participantInfo['prenom'] ?? '';
                $genre = $participantInfo['genre'] ?? 'N/A';
                $dateNaissance = $participantInfo['datenaissance'] ?? null;
                
                $numparticipant = DB::table('participant')->insertGetId([
                    'numreservation' => $numreservation,
                    'nomparticipant' => $nom,
                    'prenomparticipant' => $prenom,
                    'genreparticipant' => $genre,
                    'datenaissanceparticipant' => $dateNaissance,
                    'numtransport' => $numtransportParticipant,
                ], 'numparticipant');
                
                $participantsMap[$key] = $numparticipant;
            }
            
            for ($i = 1; $i <= $nbEnfants; $i++) {
                $key = 'enfant_' . $i;
                $numtransportParticipant = $transports[$key] ?? null;
                
                $participantInfo = $participantsData[$key] ?? [];
                $nom = $participantInfo['nom'] ?? '';
                $prenom = $participantInfo['prenom'] ?? '';
                $genre = $participantInfo['genre'] ?? 'N/A';
                $dateNaissance = $participantInfo['datenaissance'] ?? null;
                
                $numparticipant = DB::table('participant')->insertGetId([
                    'numreservation' => $numreservation,
                    'nomparticipant' => $nom,
                    'prenomparticipant' => $prenom,
                    'genreparticipant' => $genre,
                    'datenaissanceparticipant' => $dateNaissance,
                    'numtransport' => $numtransportParticipant,
                ], 'numparticipant');
                
                $participantsMap[$key] = $numparticipant;
            }

            // Ajouter les activités
            foreach ($activites as $numactivite => $participantsKeys) {
                if (is_array($participantsKeys)) {
                    foreach ($participantsKeys as $participantKey) {
                        if (isset($participantsMap[$participantKey])) {
                            DB::table('inscrire')->insert([
                                'numparticipant' => $participantsMap[$participantKey],
                                'numactivite' => $numactivite,
                            ]);
                        }
                    }
                }
            }

            // Supprimer la réservation en attente de la session
            Session::forget('pending_reservation');
            
            // Stocker l'info pour rediriger vers le panier
            Session::put('redirect_to_cart', true);
            Session::put('reservation_added', $numreservation);

        } catch (\Exception $e) {
            \Log::error('Erreur lors du traitement de la réservation en attente: ' . $e->getMessage());
            Session::forget('pending_reservation');
        }
    }

    /**
     * Récupérer le prix d'une chambre pour une date donnée
     */
    private function getPrixChambre($numtype, $dateDebut)
    {
        $dateDebut = Carbon::parse($dateDebut);
        
        // Chercher un prix promotionnel valide
        $promo = DB::table('promotion')
            ->join('tarifer', function($join) use ($numtype) {
                $join->on('promotion.numpromo', '=', 'tarifer.numpromo')
                     ->where('tarifer.numtype', '=', $numtype);
            })
            ->where('promotion.datedebut', '<=', $dateDebut)
            ->where('promotion.datefin', '>=', $dateDebut)
            ->select('tarifer.prixpromo')
            ->first();
        
        if ($promo) {
            return $promo->prixpromo;
        }
        
        // Sinon prix standard
        $typeChambre = DB::table('typechambre')->where('numtype', $numtype)->first();
        return $typeChambre->prixbase ?? 100;
    }
}

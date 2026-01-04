<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Resort;
use App\Models\Typechambre;
use App\Models\Transport;
use App\Models\Activite;
use App\Models\Partenaire;
use App\Mail\PartnerValidationMail;
use App\Mail\ResortValidationMail;
use App\Mail\PartnerConfirmationMail;

class ReservationController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $today = Carbon::today();

        // UNIQUEMENT les réservations confirmées/validées ou terminées (payées)
        $reservations = DB::table('reservation')
            ->join('resort', 'reservation.numresort', '=', 'resort.numresort') 
            ->join('pays', 'resort.codepays', '=', 'pays.codepays')
            ->join('choisir', 'reservation.numreservation', '=', 'choisir.numreservation')
            ->join('typechambre', 'choisir.numtype', '=', 'typechambre.numtype')
            ->where('reservation.user_id', $userId)
            ->whereIn('reservation.statut', ['Confirmée', 'Validée', 'Terminée'])
            ->select(
                'reservation.*', 
                'resort.nomresort', 
                'resort.numresort',
                'pays.nompays',
                'typechambre.nomtype as type_chambre'
            )
            ->distinct()
            ->orderBy('reservation.datedebut', 'desc')
            ->get();

        // UNIQUEMENT les réservations en attente (panier)
        $panierResorts = DB::table('reservation')
            ->join('resort', 'reservation.numresort', '=', 'resort.numresort') 
            ->join('pays', 'resort.codepays', '=', 'pays.codepays')
            ->join('choisir', 'reservation.numreservation', '=', 'choisir.numreservation')
            ->join('typechambre', 'choisir.numtype', '=', 'typechambre.numtype')
            ->where('reservation.user_id', $userId)
            ->where('reservation.statut', 'En attente')
            ->select(
                'reservation.*', 
                'resort.nomresort', 
                'resort.numresort',
                'pays.nompays',
                'typechambre.nomtype as type_chambre'
            )
            ->distinct()
            ->orderBy('reservation.datedebut', 'asc')
            ->get();

        $enCours = $reservations->filter(function ($res) use ($today) {
            return $today->between($res->datedebut, $res->datefin);
        });

        $aVenir = $reservations->filter(function ($res) use ($today) {
            return $res->datedebut > $today;
        });

        $terminees = $reservations->filter(function ($res) use ($today) {
            return $res->datefin < $today;
        });

        return view('reservations', [
            'enCours' => $enCours,
            'aVenir' => $aVenir,
            'terminees' => $terminees,
            'panierResorts' => $panierResorts
        ]);
    }

    public function step1($numresort)
    {
        $resort = Resort::with(['photos', 'pays'])->findOrFail($numresort);
        $typeChambres = DB::table('typechambre')
            ->join('proposer', 'typechambre.numtype', '=', 'proposer.numtype')
            ->where('proposer.numresort', $numresort)
            ->select('typechambre.*')
            ->get();

        return view('reservation.step1', compact('resort', 'typeChambres'));
    }

    public function step2(Request $request, $numresort)
    {
        $resort = Resort::with(['photos', 'pays'])->findOrFail($numresort);
        $transports = Transport::all();
        
        $dateDebut = $request->query('dateDebut');
        $dateFin = $request->query('dateFin');
        $chambres = $request->query('chambres', []); // Array [numtype => qty]
        $numtype = $request->query('numtype'); // Pour compatibilité ancienne version
        $numtransport = $request->query('numtransport');
        $nbAdultes = $request->query('nbAdultes', 1);
        $nbEnfants = $request->query('nbEnfants', 0);
        $participants = $request->query('participants', []);

        // Si anciennes données (numtype unique), convertir en format chambres
        if ($numtype && empty($chambres)) {
            $chambres = [$numtype => 1];
        }

        return view('reservation.step2', compact('resort', 'transports', 'dateDebut', 'dateFin', 'chambres', 'numtransport', 'nbAdultes', 'nbEnfants', 'participants'));
    }

    public function step3(Request $request, $numresort)
    {
        $resort = Resort::with(['photos', 'pays'])->findOrFail($numresort);
        
        $dateDebut = $request->query('dateDebut');
        $dateFin = $request->query('dateFin');
        $chambres = $request->query('chambres', []);
        $nbAdultes = $request->query('nbAdultes', 1);
        $nbEnfants = $request->query('nbEnfants', 0);
        $participants = $request->query('participants', []);
        
        // Récupérer les transports sélectionnés
        $transportsParticipants = [];
        for ($i = 1; $i <= $nbAdultes; $i++) {
            $transportsParticipants['adulte_' . $i] = $request->query('transport_adulte_' . $i);
        }
        for ($i = 1; $i <= $nbEnfants; $i++) {
            $transportsParticipants['enfant_' . $i] = $request->query('transport_enfant_' . $i);
        }

        try {
            $activites = DB::table('activite')
                ->join('activitealacarte', 'activite.numactivite', '=', 'activitealacarte.numactivite')
                ->join('typeactivite', 'activite.numtypeactivite', '=', 'typeactivite.numtypeactivite')
                ->join('partager', 'typeactivite.numtypeactivite', '=', 'partager.numtypeactivite')
                ->where('partager.numresort', $numresort)
                ->select('activite.numactivite', 'activite.nomactivite', 'activite.descriptionactivite', 'activitealacarte.prixmin')
                ->distinct()
                ->get();
        } catch (\Exception $e) {
            $activites = collect([]);
        }

        $nbNuits = Carbon::parse($dateDebut)->diffInDays(Carbon::parse($dateFin));
        
        $prixChambre = 0;
        foreach ($chambres as $numtype => $qty) {
            if ($qty > 0) {
                $prixParNuit = $this->getPrixChambre($numtype, $dateDebut);
                $prixChambre += $prixParNuit * $nbNuits * $qty;
            }
        }
        
        $prixTransport = 0;
        foreach ($transportsParticipants as $numtransport) {
            if ($numtransport) {
                $transport = Transport::find($numtransport);
                if ($transport) {
                    $prixTransport += $transport->prixtransport;
                }
            }
        }

        return view('reservation.step3', compact(
            'resort', 'activites', 'dateDebut', 'dateFin', 'chambres', 'transportsParticipants',
            'nbAdultes', 'nbEnfants', 'participants', 'nbNuits', 'prixChambre', 'prixTransport'
        ));
    }

    public function addToCart(Request $request, $numresort)
    {
        $request->validate([
            'dateDebut' => 'required|date',
            'dateFin' => 'required|date|after:dateDebut',
            'nbAdultes' => 'required|integer|min:1',
            'nbEnfants' => 'required|integer|min:0',
        ]);

        $dateDebut = $request->input('dateDebut');
        $dateFin = $request->input('dateFin');
        $chambres = $request->input('chambres', []); // Array [numtype => qty]
        $transports = $request->input('transports', []); // Array [participantKey => numtransport]
        $nbAdultes = $request->input('nbAdultes');
        $nbEnfants = $request->input('nbEnfants');
        $activites = $request->input('activites', []); // Array [numactivite => [participantKeys]]

        $nbNuits = Carbon::parse($dateDebut)->diffInDays(Carbon::parse($dateFin));
        $nbPersonnes = $nbAdultes + $nbEnfants;

        $prixChambre = 0;
        foreach ($chambres as $numtype => $qty) {
            if ($qty > 0) {
                $prixParNuit = $this->getPrixChambre($numtype, $dateDebut);
                $prixChambre += $prixParNuit * $nbNuits * $qty;
            }
        }

        $prixTransport = 0;
        foreach ($transports as $numtransport) {
            if ($numtransport) {
                $transport = Transport::find($numtransport);
                if ($transport) {
                    $prixTransport += $transport->prixtransport;
                }
            }
        }

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

        $numreservation = DB::table('reservation')->insertGetId([
            'user_id' => Auth::id(),
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

        foreach ($chambres as $numtype => $qty) {
            if ($qty > 0) {
                DB::table('choisir')->insert([
                    'numreservation' => $numreservation,
                    'numtype' => $numtype,
                    'quantite' => $qty,
                ]);
            }
        }

        $participantsMap = [];
        $participantsData = $request->input('participants', []);
        
        for ($i = 1; $i <= $nbAdultes; $i++) {
            $key = 'adulte_' . $i;
            $numtransport = $transports[$key] ?? null;
            
            // Récupérer les informations du participant
            $participantInfo = $participantsData[$key] ?? [];
            $nom = $participantInfo['nom'] ?? '';
            $prenom = $participantInfo['prenom'] ?? '';
            $genre = $participantInfo['genre'] ?? 'N/A';
            $dateNaissance = $participantInfo['datenaissance'] ?? null;
            
            // Validation de l'âge pour les adultes (>= 15 ans)
            if ($dateNaissance) {
                $age = \Carbon\Carbon::parse($dateNaissance)->age;
                if ($age < 15) {
                    return redirect()->back()
                        ->with('error', "Le participant $prenom $nom (Adulte $i) doit avoir au moins 15 ans.")
                        ->withInput();
                }
            }
            
            $numparticipant = DB::table('participant')->insertGetId([
                'numreservation' => $numreservation,
                'nomparticipant' => $nom,
                'prenomparticipant' => $prenom,
                'genreparticipant' => $genre,
                'datenaissanceparticipant' => $dateNaissance,
                'numtransport' => $numtransport,
            ], 'numparticipant');
            
            $participantsMap[$key] = $numparticipant;
        }
        
        for ($i = 1; $i <= $nbEnfants; $i++) {
            $key = 'enfant_' . $i;
            $numtransport = $transports[$key] ?? null;
            
            // Récupérer les informations du participant
            $participantInfo = $participantsData[$key] ?? [];
            $nom = $participantInfo['nom'] ?? '';
            $prenom = $participantInfo['prenom'] ?? '';
            $genre = $participantInfo['genre'] ?? 'N/A';
            $dateNaissance = $participantInfo['datenaissance'] ?? null;
            
            // Validation de l'âge pour les enfants (< 15 ans)
            if ($dateNaissance) {
                $age = \Carbon\Carbon::parse($dateNaissance)->age;
                if ($age >= 15) {
                    return redirect()->back()
                        ->with('error', "Le participant $prenom $nom (Enfant $i) doit avoir moins de 15 ans.")
                        ->withInput();
                }
            }
            
            $numparticipant = DB::table('participant')->insertGetId([
                'numreservation' => $numreservation,
                'nomparticipant' => $nom,
                'prenomparticipant' => $prenom,
                'genreparticipant' => $genre,
                'datenaissanceparticipant' => $dateNaissance,
                'numtransport' => $numtransport,
            ], 'numparticipant');
            
            $participantsMap[$key] = $numparticipant;
        }

        foreach ($activites as $numactivite => $participants) {
            if (is_array($participants) && count($participants) > 0) {
                $activite = DB::table('activitealacarte')->where('numactivite', $numactivite)->first();
                $partenaireRecord = DB::table('fourni')->where('numactivite', $numactivite)->first();

                if ($activite) {
                    if ($partenaireRecord && $partenaireRecord->numpartenaire) {
                        DB::table('reservation_activite')->insert([
                            'numreservation' => $numreservation,
                            'numactivite' => $numactivite,
                            'prix_unitaire' => $activite->prixmin,
                            'quantite' => count($participants),
                            'numpartenaire' => $partenaireRecord->numpartenaire,
                            'partenaire_validation_status' => 'pending',
                            'created_at' => now(),
                        ]);
                    }

                    foreach ($participants as $participantKey) {
                        if (isset($participantsMap[$participantKey])) {
                            DB::table('participant_activite')->insert([
                                'numparticipant' => $participantsMap[$participantKey],
                                'numactivite' => $numactivite,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            }
        }

        // L'email au resort sera envoyé après le paiement (via webhook Stripe)

        // Stocker les détails de réservation en session
        $reservationDetails = session('reservation_details', []);
        $reservationDetails[$numreservation] = [
            'nbAdultes' => $nbAdultes,
            'nbEnfants' => $nbEnfants,
        ];
        session(['reservation_details' => $reservationDetails]);

        return redirect()->route('cart.index')->with('success', 'Réservation ajoutée au panier !');
    }

    public function getPrix(Request $request)
    {
        $numtype = $request->input('numtype');
        $dateDebut = $request->input('dateDebut');
        $dateFin = $request->input('dateFin');

        if (!$numtype) {
            return response()->json(['error' => 'Paramètres manquants'], 400);
        }

        if (!$dateDebut || !$dateFin) {
            $prixParNuit = $this->getPrixChambre($numtype, now()->format('Y-m-d'));
            return response()->json([
                'prixParNuit' => $prixParNuit
            ]);
        }

        $nbNuits = Carbon::parse($dateDebut)->diffInDays(Carbon::parse($dateFin));
        $prixParNuit = $this->getPrixChambre($numtype, $dateDebut);
        $prixTotal = $prixParNuit * $nbNuits;

        return response()->json([
            'prixParNuit' => $prixParNuit,
            'nbNuits' => $nbNuits,
            'prixTotal' => $prixTotal
        ]);
    }

    private function getPrixChambre($numtype, $date)
    {
        $periode = DB::table('periode')
            ->whereDate('datedebutperiode', '<=', $date)
            ->whereDate('datefinperiode', '>=', $date)
            ->first();

        if ($periode) {
            $tarif = DB::table('tarifer')
                ->where('numtype', $numtype)
                ->where('numperiode', $periode->numperiode)
                ->first();

            if ($tarif) {
                return $tarif->prix;
            }
        }

        $tarif = DB::table('tarifer')
            ->where('numtype', $numtype)
            ->first();

        return $tarif ? $tarif->prix : 100;
    }

    public function show($numreservation)
    {
        $userId = Auth::id();

        $reservation = \App\Models\Reservation::with([
            'resort.pays', 
            'transport', 
            'activites.activite',
            'participants',
            'paiements'
        ])
        ->where('user_id', $userId)
        ->where('numreservation', $numreservation)
        ->firstOrFail();

        $typeChambre = DB::table('choisir')
            ->join('typechambre', 'choisir.numtype', '=', 'typechambre.numtype')
            ->where('choisir.numreservation', $numreservation)
            ->select('typechambre.*')
            ->first();

        return view('reservation.show', compact('reservation', 'typeChambre'));
    }

    // Édition complète (page unique) - NOUVEAU
    public function editReservation($numreservation)
    {
        $reservation = DB::table('reservation')
            ->where('numreservation', $numreservation)
            ->where('user_id', auth()->id())
            ->first();

        if (!$reservation) {
            return redirect()->route('cart.index')->with('error', 'Réservation non trouvée');
        }

        $resort = Resort::with(['photos', 'pays'])->findOrFail($reservation->numresort);

        $typeChambres = DB::table('typechambre')
            ->join('proposer', 'typechambre.numtype', '=', 'proposer.numtype')
            ->where('proposer.numresort', $reservation->numresort)
            ->select('typechambre.*')
            ->get();

        foreach ($typeChambres as $type) {
            $type->prix = $this->getPrixChambre($type->numtype, $reservation->datedebut);
        }

        $chambresSelectionnees = DB::table('choisir')
            ->where('numreservation', $numreservation)
            ->pluck('quantite', 'numtype')
            ->toArray();

        $participants = DB::table('participant')
            ->where('numreservation', $numreservation)
            ->get();

        $nbAdultes = $participants->filter(function($p) {
            if ($p->datenaissanceparticipant) {
                $age = \Carbon\Carbon::parse($p->datenaissanceparticipant)->age;
                return $age >= 15;
            }
            return str_contains($p->nomparticipant, 'Adulte');
        })->count();
        
        $nbEnfants = $participants->filter(function($p) {
            if ($p->datenaissanceparticipant) {
                $age = \Carbon\Carbon::parse($p->datenaissanceparticipant)->age;
                return $age < 15;
            }
            return str_contains($p->nomparticipant, 'Enfant');
        })->count();

        $transports = DB::table('transport')->get();

        $activites = DB::table('activite')
            ->join('activitealacarte', 'activite.numactivite', '=', 'activitealacarte.numactivite')
            ->join('typeactivite', 'activite.numtypeactivite', '=', 'typeactivite.numtypeactivite')
            ->join('partager', 'typeactivite.numtypeactivite', '=', 'partager.numtypeactivite')
            ->where('partager.numresort', $reservation->numresort)
            ->select('activite.numactivite', 'activite.nomactivite', 'activite.descriptionactivite', 'activitealacarte.prixmin')
            ->distinct()
            ->get();

        // Récupérer les activités déjà sélectionnées
        $activitesSelectionnees = [];
        foreach ($participants as $participant) {
            $activitesParticipant = DB::table('participant_activite')
                ->where('numparticipant', $participant->numparticipant)
                ->pluck('numactivite')
                ->toArray();
            $activitesSelectionnees[$participant->numparticipant] = $activitesParticipant;
        }

        return view('reservation.edit-reservation', compact(
            'reservation', 
            'resort', 
            'typeChambres', 
            'chambresSelectionnees', 
            'nbAdultes', 
            'nbEnfants', 
            'participants', 
            'transports',
            'activites',
            'activitesSelectionnees'
        ));
    }

    public function updateReservationComplete(Request $request, $numreservation)
    {
        try {
            $reservation = DB::table('reservation')
                ->where('numreservation', $numreservation)
                ->where('user_id', auth()->id())
                ->first();

            if (!$reservation) {
                return redirect()->route('cart.index')->with('error', 'Réservation non trouvée');
            }

            $request->validate([
                'dateDebut' => 'required|date',
                'dateFin' => 'required|date|after:dateDebut',
                'nbAdultes' => 'required|integer|min:1',
                'nbEnfants' => 'required|integer|min:0',
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur validation : ' . $e->getMessage())->withInput();
        }

        try {
            DB::beginTransaction();

            $nbAdultes = (int)$request->input('nbAdultes');
            $nbEnfants = (int)$request->input('nbEnfants');

            // 1. Mettre à jour les informations de base de la réservation
            DB::table('reservation')->where('numreservation', $numreservation)->update([
                'datedebut' => $request->input('dateDebut'),
                'datefin' => $request->input('dateFin'),
                'nbpersonnes' => $nbAdultes + $nbEnfants,
            ]);

            // 2. Mettre à jour les chambres sélectionnées
            DB::table('choisir')->where('numreservation', $numreservation)->delete();
            
            $chambres = $request->input('chambres', []);
            foreach ($chambres as $numtype => $quantite) {
                if ($quantite > 0) {
                    DB::table('choisir')->insert([
                        'numreservation' => $numreservation,
                        'numtype' => $numtype,
                        'quantite' => $quantite,
                    ]);
                }
            }

            // 3. Supprimer tous les participants existants et leurs activités
            $oldParticipants = DB::table('participant')
                ->where('numreservation', $numreservation)
                ->pluck('numparticipant');

            if ($oldParticipants->isNotEmpty()) {
                DB::table('participant_activite')->whereIn('numparticipant', $oldParticipants)->delete();
                DB::table('participant')->whereIn('numparticipant', $oldParticipants)->delete();
            }

            // 4. Créer les nouveaux participants (nouveau système par person_ID)
            $participantsData = $request->input('participants', []);
            $participantIds = []; // Mapper person_ID -> numparticipant

            foreach ($participantsData as $personKey => $info) {
                // Le personKey est du format "person_123"
                if (!str_starts_with($personKey, 'person_')) continue;
                
                $transportName = "transport_{$personKey}";
                $transportId = $request->input($transportName);
                
                $numparticipant = DB::table('participant')->insertGetId([
                    'numreservation' => $numreservation,
                    'nomparticipant' => $info['nom'] ?? '',
                    'prenomparticipant' => $info['prenom'] ?? '',
                    'genreparticipant' => $info['genre'] ?? 'N/A',
                    'datenaissanceparticipant' => $info['datenaissance'] ?? null,
                    'numtransport' => $transportId,
                ], 'numparticipant');

                $participantIds[$personKey] = $numparticipant;
            }

            // 5. Ajouter les activités sélectionnées (nouveau format person_ID)
            $activites = $request->input('activites', []);
            foreach ($activites as $numactivite => $participants) {
                foreach ($participants as $personKey => $selected) {
                    // personKey est du format "person_123"
                    if ($selected && isset($participantIds[$personKey])) {
                        DB::table('participant_activite')->insert([
                            'numparticipant' => $participantIds[$personKey],
                            'numactivite' => $numactivite,
                        ]);
                    }
                }
            }

            // 6. Recalculer le prix total
            $this->recalculerPrixReservation($numreservation);

            DB::commit();

            return redirect()->route('panier.show', $numreservation)
                ->with('success', 'Réservation mise à jour avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage())
                ->withInput();
        }
    }

    // ================== FONCTIONS UTILITAIRES ==================
    
    // Méthode pour recalculer le prix total d'une réservation
    private function recalculerPrixReservation($numreservation)
    {
        $reservation = DB::table('reservation')->where('numreservation', $numreservation)->first();
        
        if (!$reservation) {
            return;
        }

        $dateDebut = \Carbon\Carbon::parse($reservation->datedebut);
        $dateFin = \Carbon\Carbon::parse($reservation->datefin);
        $nbNuits = $dateDebut->diffInDays($dateFin);

        // Calcul prix chambres
        $prixChambres = 0;
        $chambres = DB::table('choisir')
            ->join('typechambre', 'choisir.numtype', '=', 'typechambre.numtype')
            ->where('choisir.numreservation', $numreservation)
            ->select('choisir.quantite', 'typechambre.numtype')
            ->get();

        foreach ($chambres as $chambre) {
            $prixParNuit = $this->getPrixChambre($chambre->numtype, $reservation->datedebut);
            $prixChambres += $prixParNuit * $nbNuits * $chambre->quantite;
        }

        // Calcul prix transport
        $prixTransport = DB::table('participant')
            ->join('transport', 'participant.numtransport', '=', 'transport.numtransport')
            ->where('participant.numreservation', $numreservation)
            ->sum('transport.prixtransport');

        // Calcul prix activités
        $prixActivites = DB::table('participant_activite')
            ->join('participant', 'participant_activite.numparticipant', '=', 'participant.numparticipant')
            ->join('activitealacarte', 'participant_activite.numactivite', '=', 'activitealacarte.numactivite')
            ->where('participant.numreservation', $numreservation)
            ->sum('activitealacarte.prixmin');

        // Total HT
        $totalHT = $prixChambres + $prixTransport + $prixActivites;

        // TVA 20%
        $tva = $totalHT * 0.20;

        // Total TTC
        $totalTTC = $totalHT + $tva;

        // Mise à jour
        DB::table('reservation')
            ->where('numreservation', $numreservation)
            ->update(['prixtotal' => $totalTTC]);
    }
}
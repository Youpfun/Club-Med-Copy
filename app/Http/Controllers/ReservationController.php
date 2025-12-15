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

        // Si anciennes données (numtype unique), convertir en format chambres
        if ($numtype && empty($chambres)) {
            $chambres = [$numtype => 1];
        }

        return view('reservation.step2', compact('resort', 'transports', 'dateDebut', 'dateFin', 'chambres', 'numtransport', 'nbAdultes', 'nbEnfants'));
    }

    public function step3(Request $request, $numresort)
    {
        $resort = Resort::with(['photos', 'pays'])->findOrFail($numresort);
        
        $dateDebut = $request->query('dateDebut');
        $dateFin = $request->query('dateFin');
        $chambres = $request->query('chambres', []);
        $nbAdultes = $request->query('nbAdultes', 1);
        $nbEnfants = $request->query('nbEnfants', 0);
        
        // Récupérer les transports sélectionnés pour chaque participant
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
        
        // Calculer le prix total des chambres
        $prixChambre = 0;
        foreach ($chambres as $numtype => $qty) {
            if ($qty > 0) {
                $prixParNuit = $this->getPrixChambre($numtype, $dateDebut);
                $prixChambre += $prixParNuit * $nbNuits * $qty;
            }
        }
        
        // Calculer le prix total du transport
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
            'nbAdultes', 'nbEnfants', 'nbNuits', 'prixChambre', 'prixTransport'
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

        // Calculer le prix total des chambres
        $prixChambre = 0;
        foreach ($chambres as $numtype => $qty) {
            if ($qty > 0) {
                $prixParNuit = $this->getPrixChambre($numtype, $dateDebut);
                $prixChambre += $prixParNuit * $nbNuits * $qty;
            }
        }

        // Calculer le prix total du transport
        $prixTransport = 0;
        foreach ($transports as $numtransport) {
            if ($numtransport) {
                $transport = Transport::find($numtransport);
                if ($transport) {
                    $prixTransport += $transport->prixtransport;
                }
            }
        }

        // Calculer le prix des activités (par participant sélectionné)
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

        // Créer les enregistrements dans choisir pour chaque type de chambre avec quantité
        foreach ($chambres as $numtype => $qty) {
            if ($qty > 0) {
                DB::table('choisir')->insert([
                    'numreservation' => $numreservation,
                    'numtype' => $numtype,
                    'quantite' => $qty,
                ]);
            }
        }

        // Créer les participants avec leur transport
        $participantsMap = [];
        
        // Créer les adultes
        for ($i = 1; $i <= $nbAdultes; $i++) {
            $key = 'adulte_' . $i;
            $numtransport = $transports[$key] ?? null;
            
            $numparticipant = DB::table('participant')->insertGetId([
                'numreservation' => $numreservation,
                'nomparticipant' => 'Adulte ' . $i,
                'prenomparticipant' => '',
                'genreparticipant' => 'N/A',
                'datenaissanceparticipant' => null,
                'numtransport' => $numtransport,
            ], 'numparticipant');
            
            $participantsMap[$key] = $numparticipant;
        }
        
        // Créer les enfants
        for ($i = 1; $i <= $nbEnfants; $i++) {
            $key = 'enfant_' . $i;
            $numtransport = $transports[$key] ?? null;
            
            $numparticipant = DB::table('participant')->insertGetId([
                'numreservation' => $numreservation,
                'nomparticipant' => 'Enfant ' . $i,
                'prenomparticipant' => '',
                'genreparticipant' => 'N/A',
                'datenaissanceparticipant' => null,
                'numtransport' => $numtransport,
            ], 'numparticipant');
            
            $participantsMap[$key] = $numparticipant;
        }

        // Créer les liens participant_activite et reservation_activite
        foreach ($activites as $numactivite => $participants) {
            if (is_array($participants) && count($participants) > 0) {
                $activite = DB::table('activitealacarte')->where('numactivite', $numactivite)->first();
                $partenaireRecord = DB::table('fourni')->where('numactivite', $numactivite)->first();

                if ($activite) {
                    // Créer un enregistrement dans reservation_activite uniquement si un partenaire existe
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

                    // Créer les liens participant_activite pour chaque participant
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

        // Envoyer un email de validation au resort
        $reservationLoaded = \App\Models\Reservation::with(['resort', 'user', 'activites.activite', 'chambres.typechambre'])->find($numreservation);
        
        // Générer un token unique pour le resort
        $resortToken = (string) Str::uuid();
        $expiresAt = now()->addDays(3);

        // Mettre à jour la réservation avec le token resort
        DB::table('reservation')
            ->where('numreservation', $numreservation)
            ->update([
                'resort_validation_token' => $resortToken,
                'resort_validation_token_expires_at' => $expiresAt,
                'resort_validation_status' => 'pending',
            ]);

        // Envoyer l'email au resort
        $resort = $reservationLoaded->resort;
        $resortEmail = $resort->emailresort ?? config('mail.from.address');
        $resortLink = url('/resort/validate/' . $resortToken);

        try {
            Mail::to($resortEmail)->send(new ResortValidationMail($reservationLoaded, $resort, $resortLink));
        } catch (\Exception $e) {
            \Log::error('Envoi email resort échec: ' . $e->getMessage());
        }

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

    // Édition Step 1 : Dates et chambres
    public function editStep1($numreservation)
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

        // Ajouter les prix pour chaque type de chambre
        foreach ($typeChambres as $type) {
            $type->prix = $this->getPrixChambre($type->numtype, $reservation->datedebut);
        }

        $chambresSelectionnees = DB::table('choisir')
            ->where('numreservation', $numreservation)
            ->pluck('quantite', 'numtype')
            ->toArray();

        return view('reservation.edit-step1', compact('reservation', 'resort', 'typeChambres', 'chambresSelectionnees'));
    }

    public function updateStep1(Request $request, $numreservation)
    {
        $reservation = DB::table('reservation')
            ->where('numreservation', $numreservation)
            ->where('user_id', auth()->id())
            ->first();

        if (!$reservation) {
            return redirect()->route('cart.index')->with('error', 'Réservation non trouvée');
        }

        // Mettre à jour les dates et nombre de personnes
        $dateDebut = $request->input('dateDebut');
        $dateFin = $request->input('dateFin');
        $nbAdultes = (int)$request->input('nbAdultes', 0);
        $nbEnfants = (int)$request->input('nbEnfants', 0);
        $nbPersonnes = $nbAdultes + $nbEnfants;

        DB::table('reservation')
            ->where('numreservation', $numreservation)
            ->update([
                'datedebut' => $dateDebut,
                'datefin' => $dateFin,
                'nbpersonnes' => $nbPersonnes,
            ]);

        // Mettre à jour les chambres
        DB::table('choisir')->where('numreservation', $numreservation)->delete();

        foreach ($request->input('chambres', []) as $numtype => $quantite) {
            if ($quantite > 0) {
                DB::table('choisir')->insert([
                    'numreservation' => $numreservation,
                    'numtype' => $numtype,
                    'quantite' => $quantite,
                ]);
            }
        }

        // Gérer les participants : créer ou supprimer selon le nouveau nombre
        $participantsActuels = DB::table('participant')
            ->where('numreservation', $numreservation)
            ->get();

        $nbParticipantsActuels = $participantsActuels->count();

        if ($nbPersonnes > $nbParticipantsActuels) {
            // Ajouter des participants
            $aAjouter = $nbPersonnes - $nbParticipantsActuels;
            for ($i = 0; $i < $aAjouter; $i++) {
                $index = $nbParticipantsActuels + $i + 1;
                $nom = ($index <= $nbAdultes) ? "Adulte $index" : "Enfant " . ($index - $nbAdultes);
                
                DB::table('participant')->insert([
                    'numreservation' => $numreservation,
                    'nomparticipant' => $nom,
                    'prenomparticipant' => '',
                    'genreparticipant' => 'N/A',
                    'datenaissanceparticipant' => null,
                    'numtransport' => null,
                ]);
            }
        } elseif ($nbPersonnes < $nbParticipantsActuels) {
            // Supprimer les participants en trop
            $aSupprimer = $nbParticipantsActuels - $nbPersonnes;
            $participantsASupprimer = $participantsActuels->sortByDesc('numparticipant')->take($aSupprimer);
            
            foreach ($participantsASupprimer as $participant) {
                // Supprimer les activités associées
                DB::table('participant_activite')
                    ->where('numparticipant', $participant->numparticipant)
                    ->delete();
                
                // Supprimer le participant
                DB::table('participant')
                    ->where('numparticipant', $participant->numparticipant)
                    ->delete();
            }
        }

        // Recalculer le prix
        $this->recalculerPrixReservation($numreservation);

        return redirect()->route('panier.show', $numreservation)
            ->with('success', 'Réservation mise à jour avec succès');
    }

    // Édition Step 2 : Transport
    public function editStep2($numreservation)
    {
        $reservation = DB::table('reservation')
            ->where('numreservation', $numreservation)
            ->where('user_id', auth()->id())
            ->first();

        if (!$reservation) {
            return redirect()->route('cart.index')->with('error', 'Réservation non trouvée');
        }

        $participants = DB::table('participant')
            ->where('numreservation', $numreservation)
            ->get();

        $transports = DB::table('transport')->get();

        return view('reservation.edit-step2', compact('reservation', 'participants', 'transports'));
    }

    public function updateStep2(Request $request, $numreservation)
    {
        $reservation = DB::table('reservation')
            ->where('numreservation', $numreservation)
            ->where('user_id', auth()->id())
            ->first();

        if (!$reservation) {
            return redirect()->route('cart.index')->with('error', 'Réservation non trouvée');
        }

        // Mettre à jour le transport pour chaque participant
        foreach ($request->input('transport', []) as $numparticipant => $numtransport) {
            DB::table('participant')
                ->where('numparticipant', $numparticipant)
                ->update(['numtransport' => $numtransport]);
        }

        // Recalculer le prix
        $this->recalculerPrixReservation($numreservation);

        return redirect()->route('panier.show', $numreservation)
            ->with('success', 'Transports mis à jour avec succès');
    }

    // Édition Step 3 : Activités
    public function editStep3($numreservation)
    {
        $reservation = DB::table('reservation')
            ->where('numreservation', $numreservation)
            ->where('user_id', auth()->id())
            ->first();

        if (!$reservation) {
            return redirect()->route('cart.index')->with('error', 'Réservation non trouvée');
        }

        $participants = DB::table('participant')
            ->where('numreservation', $numreservation)
            ->get();

        $activites = DB::table('activite')
            ->join('activitealacarte', 'activite.numactivite', '=', 'activitealacarte.numactivite')
            ->join('typeactivite', 'activite.numtypeactivite', '=', 'typeactivite.numtypeactivite')
            ->join('partager', 'typeactivite.numtypeactivite', '=', 'partager.numtypeactivite')
            ->where('partager.numresort', $reservation->numresort)
            ->select('activite.numactivite', 'activite.nomactivite', 'activite.descriptionactivite', 'activitealacarte.prixmin')
            ->distinct()
            ->get();

        // Récupérer les activités déjà sélectionnées
        $activitesSelectionnees = DB::table('participant_activite')
            ->whereIn('numparticipant', $participants->pluck('numparticipant'))
            ->get()
            ->groupBy('numparticipant')
            ->map(function ($items) {
                return $items->pluck('numactivite')->toArray();
            })
            ->toArray();

        return view('reservation.edit-step3', compact('reservation', 'participants', 'activites', 'activitesSelectionnees'));
    }

    public function updateStep3(Request $request, $numreservation)
    {
        $reservation = DB::table('reservation')
            ->where('numreservation', $numreservation)
            ->where('user_id', auth()->id())
            ->first();

        if (!$reservation) {
            return redirect()->route('cart.index')->with('error', 'Réservation non trouvée');
        }

        $participants = DB::table('participant')
            ->where('numreservation', $numreservation)
            ->get();

        // Supprimer toutes les anciennes activités
        DB::table('participant_activite')
            ->whereIn('numparticipant', $participants->pluck('numparticipant'))
            ->delete();

        DB::table('reservation_activite')
            ->where('numreservation', $numreservation)
            ->delete();

        // Ajouter les nouvelles activités
        $activitesParActivite = [];

        foreach ($request->input('activites', []) as $numparticipant => $activites) {
            foreach ($activites as $numactivite) {
                // Ajouter à participant_activite
                DB::table('participant_activite')->insert([
                    'numparticipant' => $numparticipant,
                    'numactivite' => $numactivite,
                ]);

                // Compter pour reservation_activite
                if (!isset($activitesParActivite[$numactivite])) {
                    $activitesParActivite[$numactivite] = 0;
                }
                $activitesParActivite[$numactivite]++;
            }
        }

        // Insérer dans reservation_activite
        foreach ($activitesParActivite as $numactivite => $nbparticipants) {
            DB::table('reservation_activite')->insert([
                'numreservation' => $numreservation,
                'numactivite' => $numactivite,
                'nbparticipants' => $nbparticipants,
            ]);
        }

        // Recalculer le prix
        $this->recalculerPrixReservation($numreservation);

        return redirect()->route('panier.show', $numreservation)
            ->with('success', 'Activités mises à jour avec succès');
    }

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
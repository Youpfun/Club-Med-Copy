<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Reservation;
use App\Models\Paiement;

class StripeController extends Controller
{
    public function index()
    {
        return view('index');
    }

    /**
     * Affiche la page de paiement pour une réservation
     */
    public function showPaymentPage($numreservation)
    {
        $reservation = Reservation::with(['resort', 'user'])->findOrFail($numreservation);
        
        // Vérifier que l'utilisateur est propriétaire de la réservation
        if ($reservation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Récupérer les détails de la réservation
        $details = DB::table('reservation')
            ->join('resort', 'reservation.numresort', '=', 'resort.numresort')
            ->join('choisir', 'reservation.numreservation', '=', 'choisir.numreservation')
            ->join('typechambre', 'choisir.numtype', '=', 'typechambre.numtype')
            ->where('reservation.numreservation', $numreservation)
            ->select('reservation.*', 'resort.nomresort', 'typechambre.nomtype')
            ->first();

        return view('payment.checkout', compact('reservation', 'details'));
    }

    /**
     * Crée une session Stripe pour le paiement
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'numreservation' => 'required|exists:reservation,numreservation',
        ]);

        $reservation = Reservation::findOrFail($request->numreservation);

        // Vérifier que l'utilisateur est propriétaire
        if ($reservation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Initialiser Stripe
        \Stripe\Stripe::setApiKey(config('stripe.secret_key'));

        try {
            // Créer la session de paiement
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => 'Réservation ' . $reservation->resort->nomresort,
                            'description' => 'Réservation du ' . $reservation->datedebut . ' au ' . $reservation->datefin,
                        ],
                        'unit_amount' => (int) ($reservation->prixtotal * 100), // Montant en centimes
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('payment.success', ['numreservation' => $reservation->numreservation]) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('payment.cancel', ['numreservation' => $reservation->numreservation]),
                'customer_email' => Auth::user()->email,
                'metadata' => [
                    'numreservation' => $reservation->numreservation,
                    'user_id' => Auth::id(),
                ],
            ]);

            return redirect()->away($session->url);
        } catch (\Exception $e) {
            return redirect()->route('panier.show', $reservation->numreservation)
                ->with('error', 'Erreur lors de la création de la session de paiement: ' . $e->getMessage());
        }
    }

    /**
     * Traite le succès du paiement
     */
    public function success(Request $request, $numreservation)
    {
        $reservation = Reservation::findOrFail($numreservation);

        // Vérifier que l'utilisateur est propriétaire
        if ($reservation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if (!$request->session_id) {
            return redirect()->route('panier.show', $numreservation)
                ->with('error', 'Paramètres de paiement invalides');
        }

        \Stripe\Stripe::setApiKey(config('stripe.secret_key'));

        try {
            // Récupérer les détails de la session
            $session = \Stripe\Checkout\Session::retrieve($request->session_id);

            // Vérifier que le paiement a bien été effectué
            if ($session->payment_status !== 'paid') {
                return redirect()->route('panier.show', $numreservation)
                    ->with('error', 'Le paiement n\'a pas été complété.');
            }

            // Vérifier si le paiement existe déjà pour éviter les doublons
            $existingPayment = DB::table('paiement')
                ->where('numreservation', $numreservation)
                ->where('stripe_session_id', $session->id)
                ->first();

            if (!$existingPayment) {
                // Créer un enregistrement de paiement
                DB::table('paiement')->insert([
                    'numreservation' => $numreservation,
                    'montant' => $reservation->prixtotal,
                    'statut' => 'Complété',
                    'stripe_session_id' => $session->id,
                    'stripe_payment_intent' => $session->payment_intent,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Mettre à jour le statut de la réservation seulement si elle est en attente
            $updated = DB::table('reservation')
                ->where('numreservation', $numreservation)
                ->where('statut', 'En attente')
                ->update([
                    'statut' => 'Validée'
                ]);

            // Log pour déboguer
            \Log::info('Paiement traité', [
                'numreservation' => $numreservation,
                'session_id' => $session->id,
                'updated' => $updated,
                'statut_actuel' => DB::table('reservation')->where('numreservation', $numreservation)->value('statut')
            ]);

            return redirect()->route('reservations.index')
                ->with('success', 'Paiement effectué avec succès! Votre réservation #' . $numreservation . ' est maintenant validée.');
        } catch (\Exception $e) {
            return redirect()->route('panier.show', $numreservation)
                ->with('error', 'Erreur lors de la confirmation du paiement: ' . $e->getMessage());
        }
    }

    /**
     * Traite l'annulation du paiement
     */
    public function cancel($numreservation)
    {
        $reservation = Reservation::findOrFail($numreservation);

        if ($reservation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return redirect()->route('panier.show', $numreservation)
            ->with('warning', 'Paiement annulé. Votre réservation reste en attente.');
    }
}
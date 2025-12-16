<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
                // Propager aussi la metadata vers le PaymentIntent pour le handler payment_intent.succeeded
                'payment_intent_data' => [
                    'metadata' => [
                        'numreservation' => $reservation->numreservation,
                        'user_id' => Auth::id(),
                    ],
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
            $existingPaymentQuery = DB::table('paiement')->where('numreservation', $numreservation);
            if (Schema::hasColumn('paiement', 'stripe_session_id')) {
                $existingPaymentQuery->where('stripe_session_id', $session->id);
            }
            $existingPayment = $existingPaymentQuery->first();

            if (!$existingPayment) {
                // Créer un enregistrement de paiement compatible avec le schéma actuel
                $data = [ 'numreservation' => $numreservation ];
                if (Schema::hasColumn('paiement', 'montant')) {
                    $data['montant'] = $reservation->prixtotal;
                } elseif (Schema::hasColumn('paiement', 'montantpaiement')) {
                    $data['montantpaiement'] = $reservation->prixtotal;
                }
                if (Schema::hasColumn('paiement', 'datepaiement')) {
                    $data['datepaiement'] = now()->toDateString();
                }
                if (Schema::hasColumn('paiement', 'statut')) {
                    $data['statut'] = 'Complété';
                }
                if (Schema::hasColumn('paiement', 'stripe_session_id')) {
                    $data['stripe_session_id'] = $session->id;
                }
                if (Schema::hasColumn('paiement', 'stripe_payment_intent')) {
                    $data['stripe_payment_intent'] = $session->payment_intent ?? null;
                }
                if (Schema::hasColumn('paiement', 'created_at')) {
                    $data['created_at'] = now();
                }
                if (Schema::hasColumn('paiement', 'updated_at')) {
                    $data['updated_at'] = now();
                }
                DB::table('paiement')->insert($data);
            }

            // Mettre à jour le statut de la réservation (sans dépendre de l'ancien statut)
            $updated = DB::table('reservation')
                ->where('numreservation', $numreservation)
                ->update([
                    'statut' => 'Confirmée'
                ]);

            // Log pour déboguer
            \Log::info('Paiement traité', [
                'numreservation' => $numreservation,
                'session_id' => $session->id,
                'updated' => $updated,
                'statut_actuel' => DB::table('reservation')->where('numreservation', $numreservation)->value('statut')
            ]);

            return redirect()->route('reservations.index')
                ->with('success', 'Paiement effectué avec succès! Votre réservation #' . $numreservation . ' est maintenant confirmée.');
        } catch (\Exception $e) {
            // Rediriger vers Mes réservations pour rester cohérent avec le flux souhaité
            return redirect()->route('reservations.index')
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

    /**
     * Crée une session Stripe pour payer toutes les réservations en attente de l'utilisateur
     */
    public function checkoutCart(Request $request)
    {
        $userId = Auth::id();

        $reservations = DB::table('reservation')
            ->join('resort', 'reservation.numresort', '=', 'resort.numresort')
            ->where('reservation.user_id', $userId)
            ->where('reservation.statut', 'En attente')
            ->select('reservation.*', 'resort.nomresort')
            ->get();

        if ($reservations->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Aucune réservation en attente à régler.');
        }

        \Stripe\Stripe::setApiKey(config('stripe.secret_key'));

        try {
            $lineItems = [];
            $ids = [];

            foreach ($reservations as $res) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => 'Réservation ' . $res->nomresort,
                            'description' => 'Réservation #' . $res->numreservation,
                        ],
                        'unit_amount' => (int) ($res->prixtotal * 100),
                    ],
                    'quantity' => 1,
                ];
                $ids[] = $res->numreservation;
            }

            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => route('payment.cart.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('payment.cart.cancel'),
                'customer_email' => Auth::user()->email,
                'metadata' => [
                    'numreservations' => implode(',', $ids),
                    'user_id' => $userId,
                ],
                'payment_intent_data' => [
                    'metadata' => [
                        'numreservations' => implode(',', $ids),
                        'user_id' => $userId,
                    ],
                ],
            ]);

            return redirect()->away($session->url);
        } catch (\Exception $e) {
            return redirect()->route('cart.index')
                ->with('error', 'Erreur lors de la création de la session de paiement: ' . $e->getMessage());
        }
    }

    /**
     * Succès du paiement du panier
     */
    public function successCart(Request $request)
    {
        if (!$request->session_id) {
            return redirect()->route('cart.index')->with('error', 'Paramètres de paiement invalides');
        }

        \Stripe\Stripe::setApiKey(config('stripe.secret_key'));
        try {
            $session = \Stripe\Checkout\Session::retrieve($request->session_id);
            if ($session->payment_status !== 'paid') {
                return redirect()->route('cart.index')->with('warning', 'Le paiement n\'a pas été complété.');
            }

            $ids = [];
            if (!empty($session->metadata->numreservations)) {
                $ids = array_filter(array_map('trim', explode(',', $session->metadata->numreservations)));
            }

            if (empty($ids)) {
                return redirect()->route('reservations.index')->with('warning', 'Paiement traité, mais aucune réservation associée.');
            }

            foreach ($ids as $id) {
                $reservation = Reservation::find($id);
                if (!$reservation || $reservation->user_id !== Auth::id()) {
                    continue;
                }

                $existingPaymentQuery = DB::table('paiement')
                    ->where('numreservation', $id);
                if (Schema::hasColumn('paiement', 'stripe_session_id')) {
                    $existingPaymentQuery->where('stripe_session_id', $session->id);
                }
                $existingPayment = $existingPaymentQuery->first();

                if (!$existingPayment) {
                    $data = [ 'numreservation' => $id ];
                    if (Schema::hasColumn('paiement', 'montant')) {
                        $data['montant'] = $reservation->prixtotal;
                    } elseif (Schema::hasColumn('paiement', 'montantpaiement')) {
                        $data['montantpaiement'] = $reservation->prixtotal;
                    }
                    if (Schema::hasColumn('paiement', 'datepaiement')) {
                        $data['datepaiement'] = now()->toDateString();
                    }
                    if (Schema::hasColumn('paiement', 'statut')) {
                        $data['statut'] = 'Complété';
                    }
                    if (Schema::hasColumn('paiement', 'stripe_session_id')) {
                        $data['stripe_session_id'] = $session->id;
                    }
                    if (Schema::hasColumn('paiement', 'stripe_payment_intent')) {
                        $data['stripe_payment_intent'] = $session->payment_intent ?? null;
                    }
                    if (Schema::hasColumn('paiement', 'created_at')) {
                        $data['created_at'] = now();
                    }
                    if (Schema::hasColumn('paiement', 'updated_at')) {
                        $data['updated_at'] = now();
                    }
                    DB::table('paiement')->insert($data);
                }

                DB::table('reservation')
                    ->where('numreservation', $id)
                    ->update(['statut' => 'Confirmée']);
            }

            return redirect()->route('reservations.index')->with('success', 'Paiement du panier effectué avec succès. Vos réservations sont confirmées.');
        } catch (\Exception $e) {
            return redirect()->route('cart.index')->with('error', 'Erreur lors de la confirmation du paiement: ' . $e->getMessage());
        }
    }

    public function cancelCart()
    {
        return redirect()->route('cart.index')->with('warning', 'Paiement annulé. Vos réservations restent en attente.');
    }
}
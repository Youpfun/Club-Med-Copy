<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Models\Reservation;

class StripeWebhookController extends Controller
{
    /**
     * Handle Stripe webhooks
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('stripe.webhook_secret');

        try {
            // Vérifier la signature du webhook si le secret est configuré
            if ($webhookSecret) {
                $event = \Stripe\Webhook::constructEvent(
                    $payload,
                    $sigHeader,
                    $webhookSecret
                );
            } else {
                $event = json_decode($payload);
            }

            // Traiter l'événement
            switch ($event->type) {
                case 'checkout.session.completed':
                    $session = $event->data->object;
                    $this->handleCheckoutSessionCompleted($session);
                    break;

                case 'payment_intent.succeeded':
                    $paymentIntent = $event->data->object;
                    $this->handlePaymentIntentSucceeded($paymentIntent);
                    break;

                case 'payment_intent.payment_failed':
                    $paymentIntent = $event->data->object;
                    $this->handlePaymentIntentFailed($paymentIntent);
                    break;

                default:
                    Log::info('Unhandled Stripe event', ['type' => $event->type]);
            }

            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            Log::error('Stripe webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Handle successful checkout session
     */
    private function handleCheckoutSessionCompleted($session)
    {
        try {
            // Gérer 1 ou plusieurs réservations via metadata
            $ids = [];
            if (!empty($session->metadata->numreservation)) {
                $ids = [$session->metadata->numreservation];
            }
            if (!empty($session->metadata->numreservations)) {
                $multi = array_filter(array_map('trim', explode(',', $session->metadata->numreservations)));
                $ids = array_merge($ids, $multi);
            }

            if (empty($ids)) {
                Log::error('No reservation ID in webhook session', ['session_id' => $session->id]);
                return;
            }

            foreach (array_unique($ids) as $numreservation) {
                $reservation = Reservation::find($numreservation);
                if (!$reservation) {
                    Log::error('Reservation not found in webhook', ['numreservation' => $numreservation]);
                    continue;
                }

                // Vérifier si le paiement existe déjà pour cette réservation
                $paymentQuery = DB::table('paiement')->where('numreservation', $numreservation);
                if (Schema::hasColumn('paiement', 'stripe_session_id')) {
                    $paymentQuery->where('stripe_session_id', $session->id);
                }
                $existingPayment = $paymentQuery->first();

                if (!$existingPayment) {
                    $data = [
                        'numreservation' => $numreservation,
                    ];
                    // Mapper le montant selon le schéma présent
                    if (Schema::hasColumn('paiement', 'montant')) {
                        $data['montant'] = $reservation->prixtotal;
                    } elseif (Schema::hasColumn('paiement', 'montantpaiement')) {
                        $data['montantpaiement'] = $reservation->prixtotal;
                    }
                    // Date de paiement si la colonne existe
                    if (Schema::hasColumn('paiement', 'datepaiement')) {
                        $data['datepaiement'] = now()->toDateString();
                    }
                    // Statut si la colonne existe
                    if (Schema::hasColumn('paiement', 'statut')) {
                        $data['statut'] = 'Complété';
                    }
                    // Colonnes Stripe si elles existent
                    if (Schema::hasColumn('paiement', 'stripe_session_id')) {
                        $data['stripe_session_id'] = $session->id;
                    }
                    if (Schema::hasColumn('paiement', 'stripe_payment_intent')) {
                        $data['stripe_payment_intent'] = $session->payment_intent ?? null;
                    }
                    // Timestamps si présents
                    if (Schema::hasColumn('paiement', 'created_at')) {
                        $data['created_at'] = now();
                    }
                    if (Schema::hasColumn('paiement', 'updated_at')) {
                        $data['updated_at'] = now();
                    }

                    DB::table('paiement')->insert($data);
                }

                DB::table('reservation')
                    ->where('numreservation', $numreservation)
                    ->update(['statut' => 'Confirmée']);

                Log::info('Webhook: Reservation updated', [
                    'numreservation' => $numreservation,
                    'statut' => DB::table('reservation')->where('numreservation', $numreservation)->value('statut')
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error handling checkout session', [
                'error' => $e->getMessage(),
                'session_id' => $session->id ?? 'unknown'
            ]);
        }
    }

    /**
     * Handle successful payment intent
     */
    private function handlePaymentIntentSucceeded($paymentIntent)
    {
        try {
            Log::info('Payment Intent succeeded', [
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $paymentIntent->amount / 100,
                'currency' => $paymentIntent->currency
            ]);

            // Récupérer les réservations liées via le checkout session
            if (!empty($paymentIntent->metadata->numreservation)) {
                $numreservation = $paymentIntent->metadata->numreservation;
                
                $reservation = Reservation::find($numreservation);
                if ($reservation && $reservation->statut !== 'Confirmée') {
                    DB::table('reservation')
                        ->where('numreservation', $numreservation)
                        ->update(['statut' => 'Confirmée']);

                    // Insérer un paiement minimal si aucune ligne n'existe et que le schéma ne gène pas
                    $exists = DB::table('paiement')->where('numreservation', $numreservation)->exists();
                    if (!$exists) {
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
                        if (Schema::hasColumn('paiement', 'stripe_payment_intent')) {
                            $data['stripe_payment_intent'] = $paymentIntent->id;
                        }
                        if (Schema::hasColumn('paiement', 'created_at')) {
                            $data['created_at'] = now();
                        }
                        if (Schema::hasColumn('paiement', 'updated_at')) {
                            $data['updated_at'] = now();
                        }
                        DB::table('paiement')->insert($data);
                    }

                    Log::info('Payment Intent: Reservation confirmed', [
                        'numreservation' => $numreservation,
                        'payment_intent_id' => $paymentIntent->id
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error handling payment intent succeeded', [
                'error' => $e->getMessage(),
                'payment_intent_id' => $paymentIntent->id ?? 'unknown'
            ]);
        }
    }

    /**
     * Handle failed payment intent
     */
    private function handlePaymentIntentFailed($paymentIntent)
    {
        try {
            Log::warning('Payment Intent failed', [
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $paymentIntent->amount / 100,
                'currency' => $paymentIntent->currency,
                'failure_message' => $paymentIntent->last_payment_error->message ?? 'Unknown error'
            ]);

            // Optionnel : marquer la réservation comme "Paiement échoué"
            if (!empty($paymentIntent->metadata->numreservation)) {
                $numreservation = $paymentIntent->metadata->numreservation;
                
                Log::info('Payment failed for reservation', [
                    'numreservation' => $numreservation,
                    'payment_intent_id' => $paymentIntent->id
                ]);

                // Vous pouvez ajouter une logique ici pour notifier l'utilisateur
                // ou mettre à jour un champ spécifique dans la réservation
            }
        } catch (\Exception $e) {
            Log::error('Error handling payment intent failed', [
                'error' => $e->getMessage(),
                'payment_intent_id' => $paymentIntent->id ?? 'unknown'
            ]);
        }
    }
}

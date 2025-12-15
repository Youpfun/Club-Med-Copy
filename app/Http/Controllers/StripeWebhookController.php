<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
                    Log::info('Payment Intent succeeded', ['payment_intent' => $paymentIntent->id]);
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
                $existingPayment = DB::table('paiement')
                    ->where('numreservation', $numreservation)
                    ->where('stripe_session_id', $session->id)
                    ->first();

                if (!$existingPayment) {
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
}

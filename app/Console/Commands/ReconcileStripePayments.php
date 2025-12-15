<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReconcileStripePayments extends Command
{
    protected $signature = 'payments:reconcile {--since=24 : Nombre d\'heures à regarder en arrière}';
    protected $description = 'Vérifie auprès de Stripe les paiements réussis et met à jour les réservations en "Confirmée"';

    public function handle(): int
    {
        $secret = config('stripe.secret_key');
        if (!$secret) {
            $this->error('STRIPE_SECRET_KEY manquant.');
            return self::FAILURE;
        }

        \Stripe\Stripe::setApiKey($secret);

        $hours = (int) $this->option('since');
        $sinceTimestamp = now()->subHours($hours)->timestamp;

        // Récupérer les réservations en attente récentes
        $pending = DB::table('reservation')
            ->where('statut', 'En attente')
            ->where('created_at', '>=', now()->subDays(30))
            ->pluck('numreservation');

        $updatedCount = 0;
        foreach ($pending as $numreservation) {
            try {
                // Utilise la Search API pour trouver les PaymentIntents réussis avec notre metadata
                // Voir: https://stripe.com/docs/search
                $query = "metadata['numreservation']:'{$numreservation}' AND status:'succeeded' AND created>='{$sinceTimestamp}'";
                $results = \Stripe\PaymentIntent::search(['query' => $query, 'limit' => 1]);

                if (isset($results->data[0])) {
                    $pi = $results->data[0];

                    // Éviter les doublons de paiements
                    $exists = DB::table('paiement')
                        ->where('numreservation', $numreservation)
                        ->where(function ($q) use ($pi) { $q->where('stripe_payment_intent', $pi->id); })
                        ->exists();

                    if (!$exists) {
                        // Montant en euros si disponible, sinon fallback au total local
                        $montant = $pi->amount_received ? $pi->amount_received / 100 : DB::table('reservation')->where('numreservation', $numreservation)->value('prixtotal');

                        DB::table('paiement')->insert([
                            'numreservation' => $numreservation,
                            'montant' => $montant,
                            'statut' => 'Complété',
                            'stripe_session_id' => null,
                            'stripe_payment_intent' => $pi->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    DB::table('reservation')
                        ->where('numreservation', $numreservation)
                        ->update(['statut' => 'Confirmée']);

                    $updatedCount++;
                }
            } catch (\Throwable $e) {
                Log::warning('Reconcile error', ['numreservation' => $numreservation, 'error' => $e->getMessage()]);
                continue;
            }
        }

        $this->info("Réservations mises à jour: {$updatedCount}");
        return self::SUCCESS;
    }
}

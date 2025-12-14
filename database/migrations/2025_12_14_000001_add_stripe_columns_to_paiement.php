<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Vérifier si les colonnes Stripe existent et les ajouter si nécessaire
        if (Schema::hasTable('paiement') && !Schema::hasColumn('paiement', 'stripe_session_id')) {
            Schema::table('paiement', function (Blueprint $table) {
                $table->string('stripe_session_id')->nullable();
                $table->string('stripe_payment_intent')->nullable();
            });
        }

        // Ajouter les colonnes de statut si elles n'existent pas
        if (Schema::hasTable('paiement') && !Schema::hasColumn('paiement', 'statut')) {
            Schema::table('paiement', function (Blueprint $table) {
                $table->enum('statut', ['En attente', 'Complété', 'Échoué', 'Remboursé'])->default('En attente')->after('montantpaiement')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

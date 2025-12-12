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
        // Vérifier si la colonne existe déjà, sinon l'ajouter
        if (Schema::hasTable('reservation_activite') && !Schema::hasColumn('reservation_activite', 'numpartenaire')) {
            Schema::table('reservation_activite', function (Blueprint $table) {
                $table->integer('numpartenaire')->unsigned()->nullable();
                $table->foreign('numpartenaire')->references('numpartenaire')->on('partenaire')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('reservation_activite') && Schema::hasColumn('reservation_activite', 'numpartenaire')) {
            Schema::table('reservation_activite', function (Blueprint $table) {
                $table->dropForeign(['numpartenaire']);
                $table->dropColumn('numpartenaire');
            });
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('demande_disponibilite')) {
            Schema::create('demande_disponibilite', function (Blueprint $table) {
                $table->id('numdemande');
                $table->unsignedBigInteger('numresort');
                $table->unsignedBigInteger('user_id'); // Marketing user qui a créé la demande
                $table->date('date_debut');
                $table->date('date_fin');
                $table->integer('nb_chambres')->default(1);
                $table->string('types_chambres')->nullable(); // JSON des types demandés
                $table->integer('nb_personnes')->nullable();
                $table->text('message')->nullable(); // Message du marketing
                $table->string('statut')->default('pending'); // pending, responded, expired
                $table->text('response_message')->nullable(); // Réponse du resort
                $table->string('response_status')->nullable(); // available, partially_available, not_available
                $table->json('response_details')->nullable(); // Détails de disponibilité
                $table->timestamp('responded_at')->nullable();
                $table->string('validation_token')->unique()->nullable();
                $table->timestamp('validation_token_expires_at')->nullable();
                $table->timestamps();
                
                $table->foreign('numresort')->references('numresort')->on('resort');
                $table->foreign('user_id')->references('id')->on('users');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('demande_disponibilite');
    }
};

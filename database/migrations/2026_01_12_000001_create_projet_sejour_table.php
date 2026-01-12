<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projet_sejour', function (Blueprint $table) {
            $table->id('numprojet');
            $table->unsignedBigInteger('user_id'); // Créateur (membre marketing)
            
            // Informations du séjour
            $table->string('nom_sejour', 255);
            $table->text('description')->nullable();
            $table->string('pays', 100);
            $table->string('ville', 100)->nullable();
            $table->integer('nb_tridents')->default(3);
            $table->date('date_debut_prevue')->nullable();
            $table->date('date_fin_prevue')->nullable();
            
            // Liens vers les prospections
            $table->unsignedBigInteger('prospection_resort_id')->nullable();
            $table->json('prospection_partenaires_ids')->nullable(); // Liste des IDs partenaires
            
            // Informations commerciales prévisionnelles
            $table->decimal('budget_estime', 12, 2)->nullable();
            $table->integer('capacite_prevue')->nullable(); // Nombre de chambres
            $table->text('activites_prevues')->nullable();
            $table->text('points_forts')->nullable();
            
            // Workflow de validation
            $table->enum('statut', [
                'brouillon',           // En cours de rédaction
                'soumis',              // Soumis au directeur des ventes
                'en_revision',         // Retourné pour modifications
                'approuve',            // Approuvé par le directeur
                'refuse',              // Refusé définitivement
                'en_creation'          // En cours de création effective du resort
            ])->default('brouillon');
            
            $table->unsignedBigInteger('directeur_id')->nullable(); // Directeur qui a traité
            $table->text('commentaire_directeur')->nullable();
            $table->timestamp('date_soumission')->nullable();
            $table->timestamp('date_decision')->nullable();
            
            // Lien vers le resort créé (si approuvé et créé)
            $table->unsignedBigInteger('numresort')->nullable();
            
            $table->timestamps();
            
            // Clés étrangères
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('prospection_resort_id')->references('numprospection')->on('prospection_resort')->onDelete('set null');
            $table->foreign('directeur_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('numresort')->references('numresort')->on('resort')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projet_sejour');
    }
};

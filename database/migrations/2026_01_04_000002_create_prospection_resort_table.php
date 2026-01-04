<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prospection_resort', function (Blueprint $table) {
            $table->id('numprospection');
            $table->unsignedBigInteger('user_id');
            
            // Informations sur le resort potentiel
            $table->string('nom_resort', 255);
            $table->string('email_resort', 255);
            $table->string('pays', 100)->nullable();
            $table->string('ville', 100)->nullable();
            $table->string('telephone', 50)->nullable();
            
            // Contenu de la demande
            $table->string('objet', 255);
            $table->text('message');
            
            // Statut
            $table->enum('statut', ['envoyee', 'repondue', 'en_cours', 'cloturee'])->default('envoyee');
            $table->text('reponse')->nullable();
            $table->timestamp('date_reponse')->nullable();
            
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prospection_resort');
    }
};

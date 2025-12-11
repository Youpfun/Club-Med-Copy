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
        Schema::create('signalements', function (Blueprint $table) {
            $table->bigIncrements('numsignalement');
            $table->unsignedBigInteger('numresort'); // ID du resort signalé
            $table->unsignedBigInteger('numavis'); // ID de l'avis signalé
            $table->unsignedBigInteger('user_id')->nullable(); // ID de l'utilisateur qui signale (peut être null pour visiteur anonyme)
            $table->text('message'); // Message du signalement
            $table->timestamp('datesignalement')->useCurrent();
            $table->boolean('traite')->default(false); // Indique si le signalement a été traité
            
            $table->foreign('numresort')->references('numresort')->on('resort')->onDelete('cascade');
            $table->foreign('numavis')->references('numavis')->on('avis')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            
            $table->index('numresort');
            $table->index('numavis');
            $table->index('traite');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signalements');
    }
};

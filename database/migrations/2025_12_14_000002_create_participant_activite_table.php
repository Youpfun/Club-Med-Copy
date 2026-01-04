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
        if (!Schema::hasTable('participant_activite')) {
            Schema::create('participant_activite', function (Blueprint $table) {
                $table->integer('numparticipant');
                $table->integer('numactivite');
                $table->integer('numreservation');
                
                $table->primary(['numparticipant', 'numactivite']);
                
                $table->foreign('numparticipant')->references('numparticipant')->on('participant')->onDelete('cascade');
                $table->foreign('numactivite')->references('numactivite')->on('activite')->onDelete('cascade');
                $table->foreign('numreservation')->references('numreservation')->on('reservation')->onDelete('cascade');
                
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participant_activite');
    }
};

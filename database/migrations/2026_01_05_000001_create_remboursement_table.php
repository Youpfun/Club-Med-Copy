<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('remboursement')) {
            Schema::create('remboursement', function (Blueprint $table) {
                $table->id('numremboursement');
                $table->unsignedBigInteger('numreservation')->index();
                $table->unsignedBigInteger('user_id')->index()->nullable()->comment('Utilisateur qui a initié le remboursement');
                $table->decimal('montant', 10, 2)->comment('Montant du remboursement en euros');
                $table->string('statut')->default('en_attente')->comment('en_attente, traite, refuse, rembourse');
                $table->string('raison')->nullable()->comment('Raison du remboursement');
                $table->text('notes')->nullable()->comment('Notes supplémentaires');
                $table->string('stripe_refund_id')->nullable()->comment('ID du remboursement Stripe');
                $table->string('methode_remboursement')->nullable()->comment('stripe, virement, cheque, etc.');
                $table->timestamp('date_demande')->useCurrent()->comment('Date de la demande de remboursement');
                $table->timestamp('date_traitement')->nullable()->comment('Date du traitement effectif');
                $table->timestamps();

                $table->foreign('numreservation')->references('numreservation')->on('reservation')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('remboursement');
    }
};

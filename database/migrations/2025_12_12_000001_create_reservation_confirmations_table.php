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
        if (!Schema::hasTable('reservation_confirmations')) {
            Schema::create('reservation_confirmations', function (Blueprint $table) {
                $table->id();
                $table->integer('numreservation')->unsigned();
                $table->integer('user_id')->unsigned();
                $table->boolean('notify_resort')->default(true);
                $table->boolean('notify_partenaires')->default(true);
                $table->text('confirmation_message')->nullable();
                $table->timestamp('confirmed_at');
                $table->timestamps();

                $table->foreign('numreservation')->references('numreservation')->on('reservation')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

                $table->index('numreservation');
                $table->index('user_id');
                $table->index('confirmed_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_confirmations');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservation_rejections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('numreservation')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->text('reason');
            $table->text('notes')->nullable();
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->string('refund_status')->default('pending');
            $table->timestamp('rejected_at');
            $table->timestamps();

            $table->foreign('numreservation')->references('numreservation')->on('reservation')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservation_rejections');
    }
};

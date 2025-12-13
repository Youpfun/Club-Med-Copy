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
        Schema::table('reservation', function (Blueprint $table) {
            $table->string('resort_validation_status')->default('pending')->after('statut'); // pending, accepted, refused
            $table->timestamp('resort_validated_at')->nullable()->after('resort_validation_status');
            $table->string('resort_validation_token')->nullable()->unique()->after('resort_validated_at');
            $table->timestamp('resort_validation_token_expires_at')->nullable()->after('resort_validation_token');
            $table->timestamp('resort_validation_token_used_at')->nullable()->after('resort_validation_token_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservation', function (Blueprint $table) {
            $table->dropColumn([
                'resort_validation_status',
                'resort_validated_at',
                'resort_validation_token',
                'resort_validation_token_expires_at',
                'resort_validation_token_used_at',
            ]);
        });
    }
};

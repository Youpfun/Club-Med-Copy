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
            // Colonnes pour la proposition de resort alternatif
            if (!Schema::hasColumn('reservation', 'alternative_resort_id')) {
                $table->unsignedBigInteger('alternative_resort_id')->nullable()->after('numresort');
            }
            if (!Schema::hasColumn('reservation', 'alternative_resort_status')) {
                $table->string('alternative_resort_status')->default('none')->after('alternative_resort_id');
            }
            if (!Schema::hasColumn('reservation', 'alternative_resort_proposed_at')) {
                $table->timestamp('alternative_resort_proposed_at')->nullable()->after('alternative_resort_status');
            }
            if (!Schema::hasColumn('reservation', 'alternative_resort_token')) {
                $table->string('alternative_resort_token')->nullable()->unique()->after('alternative_resort_proposed_at');
            }
            if (!Schema::hasColumn('reservation', 'alternative_resort_token_expires_at')) {
                $table->timestamp('alternative_resort_token_expires_at')->nullable()->after('alternative_resort_token');
            }
            if (!Schema::hasColumn('reservation', 'alternative_resort_responded_at')) {
                $table->timestamp('alternative_resort_responded_at')->nullable()->after('alternative_resort_token_expires_at');
            }
            if (!Schema::hasColumn('reservation', 'alternative_resort_message')) {
                $table->text('alternative_resort_message')->nullable()->after('alternative_resort_responded_at');
            }
            if (!Schema::hasColumn('reservation', 'alternative_proposed_by')) {
                $table->unsignedBigInteger('alternative_proposed_by')->nullable()->after('alternative_resort_message');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservation', function (Blueprint $table) {
            $table->dropColumn([
                'alternative_resort_id',
                'alternative_resort_status',
                'alternative_resort_proposed_at',
                'alternative_resort_token',
                'alternative_resort_token_expires_at',
                'alternative_resort_responded_at',
                'alternative_resort_message',
                'alternative_proposed_by',
            ]);
        });
    }
};

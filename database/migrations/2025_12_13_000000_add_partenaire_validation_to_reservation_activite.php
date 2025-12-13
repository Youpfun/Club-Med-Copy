<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservation_activite', function (Blueprint $table) {
            if (!Schema::hasColumn('reservation_activite', 'partenaire_validation_status')) {
                $table->string('partenaire_validation_status')->default('pending')->after('numpartenaire');
            }
            if (!Schema::hasColumn('reservation_activite', 'partenaire_validated_at')) {
                $table->timestamp('partenaire_validated_at')->nullable()->after('partenaire_validation_status');
            }
            if (!Schema::hasColumn('reservation_activite', 'validation_token')) {
                $table->string('validation_token')->nullable()->after('partenaire_validated_at');
            }
            if (!Schema::hasColumn('reservation_activite', 'validation_token_expires_at')) {
                $table->timestamp('validation_token_expires_at')->nullable()->after('validation_token');
            }
            if (!Schema::hasColumn('reservation_activite', 'validation_token_used_at')) {
                $table->timestamp('validation_token_used_at')->nullable()->after('validation_token_expires_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('reservation_activite', function (Blueprint $table) {
            $table->dropColumn([
                'partenaire_validation_status',
                'partenaire_validated_at',
                'validation_token',
                'validation_token_expires_at',
                'validation_token_used_at',
            ]);
        });
    }
};

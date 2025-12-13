<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservation_activite', function (Blueprint $table) {
            if (!Schema::hasColumn('reservation_activite', 'numpartenaire')) {
                $table->unsignedBigInteger('numpartenaire')->nullable()->after('quantite');
                $table->foreign('numpartenaire')->references('numpartenaire')->on('partenaire')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('reservation_activite', function (Blueprint $table) {
            if (Schema::hasColumn('reservation_activite', 'numpartenaire')) {
                $table->dropForeign(['numpartenaire']);
                $table->dropColumn('numpartenaire');
            }
        });
    }
};

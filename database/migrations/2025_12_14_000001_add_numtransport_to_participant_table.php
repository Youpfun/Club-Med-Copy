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
        Schema::table('participant', function (Blueprint $table) {
            $table->integer('numtransport')->nullable()->after('datenaissanceparticipant');
            $table->foreign('numtransport')->references('numtransport')->on('transport')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participant', function (Blueprint $table) {
            $table->dropForeign(['numtransport']);
            $table->dropColumn('numtransport');
        });
    }
};

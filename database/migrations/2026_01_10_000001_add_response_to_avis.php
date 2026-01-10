<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('avis', 'reponse')) {
            Schema::table('avis', function (Blueprint $table) {
                $table->text('reponse')->nullable()->after('commentaire');
                $table->unsignedBigInteger('reponse_user_id')->nullable()->after('reponse');
                $table->timestamp('date_reponse')->nullable()->after('reponse_user_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('avis', function (Blueprint $table) {
            $table->dropColumn(['reponse', 'reponse_user_id', 'date_reponse']);
        });
    }
};

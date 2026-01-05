<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Migrer les données de reservation_rejections vers remboursement si la table existe
        if (Schema::hasTable('reservation_rejections') && Schema::hasTable('remboursement')) {
            $rejections = DB::table('reservation_rejections')->get();
            
            foreach ($rejections as $rejection) {
                // Vérifier si un remboursement existe déjà pour cette réservation
                $exists = DB::table('remboursement')
                    ->where('numreservation', $rejection->numreservation)
                    ->exists();
                
                if (!$exists) {
                    // Gérer les colonnes qui peuvent ne pas exister
                    $refundStatus = $rejection->refund_status ?? 'pending';
                    $statut = $refundStatus === 'pending' ? 'en_attente' : ($refundStatus === 'completed' ? 'rembourse' : 'en_attente');
                    
                    DB::table('remboursement')->insert([
                        'numreservation' => $rejection->numreservation,
                        'user_id' => $rejection->user_id,
                        'montant' => $rejection->refund_amount ?? 0,
                        'statut' => $statut,
                        'raison' => $rejection->reason ?? $rejection->rejection_reason ?? 'Rejet de réservation',
                        'notes' => $rejection->notes ?? null,
                        'date_demande' => $rejection->rejected_at ?? $rejection->created_at ?? now(),
                        'created_at' => $rejection->created_at ?? now(),
                        'updated_at' => $rejection->updated_at ?? now(),
                    ]);
                }
            }
            
            // Supprimer l'ancienne table
            Schema::dropIfExists('reservation_rejections');
        }
    }

    public function down(): void
    {
        // Ne pas recréer l'ancienne table
    }
};

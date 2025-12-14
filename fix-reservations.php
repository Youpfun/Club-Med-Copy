<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Correction des réservations ===\n\n";

// Forcer la réservation #109 à Validée
echo "Force mise à jour réservation #109...\n";
DB::table('reservation')
    ->where('numreservation', 109)
    ->update(['statut' => 'Validée']);
echo "✓ Réservation #109 mise à jour\n\n";

// Trouver les réservations qui ont un paiement complété mais sont encore "En attente"
echo "Recherche d'autres réservations à corriger...\n";
$reservationsToFix = DB::table('reservation')
    ->whereIn('numreservation', function($query) {
        $query->select('numreservation')
              ->from('paiement')
              ->where('statut', 'Complété');
    })
    ->where('statut', 'En attente')
    ->get(['numreservation', 'statut']);

echo "Réservations à corriger: " . $reservationsToFix->count() . "\n";

if ($reservationsToFix->count() > 0) {
    foreach ($reservationsToFix as $res) {
        echo "- Réservation #{$res->numreservation}\n";
    }
    
    // Mettre à jour
    $updated = DB::table('reservation')
        ->whereIn('numreservation', function($query) {
            $query->select('numreservation')
                  ->from('paiement')
                  ->where('statut', 'Complété');
        })
        ->where('statut', 'En attente')
        ->update(['statut' => 'Validée']);
    
    echo "\n✓ {$updated} réservation(s) corrigée(s)!\n";
} else {
    echo "Aucune autre réservation à corriger.\n";
}

// Vérifications finales
echo "\n=== Vérifications finales ===\n";
$reservations = [107, 109];
foreach ($reservations as $numres) {
    $res = DB::table('reservation')->where('numreservation', $numres)->first(['numreservation', 'statut']);
    if ($res) {
        echo "Réservation #{$numres}: {$res->statut}\n";
    }
}

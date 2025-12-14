<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory;

    protected $table = 'paiement';

    protected $primaryKey = 'numpaiement';

    protected $fillable = [
        'numreservation',
        'montant',
        'statut',
        'stripe_session_id',
        'stripe_payment_intent',
        'datepaiement',
        'montantpaiement',
    ];

    protected $casts = [
        'datepaiement' => 'date',
        'montant' => 'decimal:2',
        'montantpaiement' => 'decimal:2',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'numreservation', 'numreservation');
    }
}
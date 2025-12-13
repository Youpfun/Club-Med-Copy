<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory;

    protected $table = 'paiement';

    protected $primaryKey = 'numeropaiement';

    public $timestamps = false;

    protected $fillable = [
        'numreservation',
        'datepaiement',
        'montantpaiement',
    ];

    protected $casts = [
        'datepaiement' => 'date',
        'montantpaiement' => 'decimal:2',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'numreservation', 'numreservation');
    }
}
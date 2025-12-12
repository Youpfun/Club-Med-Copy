<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationActivite extends Model
{
    protected $table = 'reservation_activite';

    protected $fillable = [
        'numreservation',
        'numactivite',
        'prix_unitaire',
        'quantite',
    ];

    public $timestamps = false;

    protected $casts = [
        'prix_unitaire' => 'decimal:2',
        'quantite' => 'integer',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'numreservation', 'numreservation');
    }

    public function activite()
    {
        return $this->belongsTo(Activite::class, 'numactivite', 'numactivite');
    }

    public function getPrixTotalAttribute()
    {
        return $this->prix_unitaire * $this->quantite;
    }
}

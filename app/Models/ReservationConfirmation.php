<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationConfirmation extends Model
{
    use HasFactory;

    protected $table = 'reservation_confirmations';

    protected $fillable = [
        'numreservation',
        'user_id',
        'notify_resort',
        'notify_partenaires',
        'confirmation_message',
        'confirmed_at',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
        'notify_resort' => 'boolean',
        'notify_partenaires' => 'boolean',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'numreservation', 'numreservation');
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}

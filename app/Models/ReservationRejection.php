<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationRejection extends Model
{
    protected $table = 'reservation_rejections';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'numreservation',
        'user_id',
        'reason',
        'notes',
        'refund_amount',
        'refund_status',
        'rejected_at',
    ];

    protected $casts = [
        'rejected_at' => 'datetime',
        'refund_amount' => 'decimal:2',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'numreservation', 'numreservation');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

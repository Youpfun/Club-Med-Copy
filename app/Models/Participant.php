<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;

    protected $table = 'participant';

    protected $primaryKey = 'numparticipant';

    public $timestamps = false;

    protected $fillable = [
        'numreservation',
        'nomparticipant',
        'prenomparticipant',
        'genreparticipant',
        'datenaissanceparticipant',
    ];

    protected $casts = [
        'datenaissanceparticipant' => 'date',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'numreservation', 'numreservation');
    }
}
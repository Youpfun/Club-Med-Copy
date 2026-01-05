<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $table = 'reservation';
    protected $primaryKey = 'numreservation';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'numresort',
        'numjour',
        'pla_numjour',
        'numtransport',
        'statut',
        'nbpersonnes',
        'prixtotal',
        'datedebut',
        'datefin',
        'resort_validation_status',
        'resort_validated_at',
        'resort_validation_token',
        'resort_validation_token_expires_at',
        'resort_validation_token_used_at',
        'alternative_resort_id',
        'alternative_resort_status',
        'alternative_resort_proposed_at',
        'alternative_resort_token',
        'alternative_resort_token_expires_at',
        'alternative_resort_responded_at',
        'alternative_resort_message',
        'alternative_proposed_by',
    ];

    protected $casts = [
        'datedebut' => 'date',
        'datefin' => 'date',
        'alternative_resort_token_expires_at' => 'datetime',
        'alternative_resort_proposed_at' => 'datetime',
        'alternative_resort_responded_at' => 'datetime',
    ];

    public function resort()
    {
        return $this->belongsTo(Resort::class, 'numresort', 'numresort');
    }

    public function alternativeResort()
    {
        return $this->belongsTo(Resort::class, 'alternative_resort_id', 'numresort');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function transport()
    {
        return $this->belongsTo(Transport::class, 'numtransport', 'numtransport');
    }

    public function chambres()
    {
        return $this->hasMany(Choisir::class, 'numreservation', 'numreservation');
    }

    public function activites()
    {
        return $this->hasMany(ReservationActivite::class, 'numreservation', 'numreservation');
    }

    public function participants()
    {
        return $this->hasMany(Participant::class, 'numreservation', 'numreservation');
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class, 'numreservation', 'numreservation');
    }

    public function partenaires()
    {
        return $this->hasManyThrough(
            Partenaire::class,
            ReservationActivite::class,
            'numreservation',
            'numpartenaire',
            'numreservation',
            'numpartenaire'
        );
    }

    public function remboursements()
    {
        return $this->hasMany(Remboursement::class, 'numreservation', 'numreservation');
    }

    public function remboursement()
    {
        return $this->hasOne(Remboursement::class, 'numreservation', 'numreservation')->latest('date_demande');
    }
}
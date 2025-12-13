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
    ];

    protected $casts = [
        'datedebut' => 'date',
        'datefin' => 'date',
    ];

    public function resort()
    {
        return $this->belongsTo(Resort::class, 'numresort', 'numresort');
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

    public function rejections()
    {
        return $this->hasMany(ReservationRejection::class, 'numreservation', 'numreservation');
    }

    public function rejection()
    {
        return $this->hasOne(ReservationRejection::class, 'numreservation', 'numreservation')->latest('rejected_at');
    }
}
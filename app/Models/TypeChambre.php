<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeChambre extends Model
{
    use HasFactory;

    protected $table = 'typechambre';
    protected $primaryKey = 'numtype';
    public $timestamps = false;

    protected $fillable = [
        'nomtype',
        'surface',
        'capacitemax',
        'textepresentation'
    ];

    /**
     * Relation avec les chambres
     */
    public function chambres()
    {
        return $this->hasMany(Chambre::class, 'numtype', 'numtype');
    }

    /**
     * Relation avec les resorts via la table proposer
     */
    public function resorts()
    {
        return $this->belongsToMany(Resort::class, 'proposer', 'numtype', 'numresort');
    }

    /**
     * Relation avec les photos via la table illustrer
     */
    public function photos()
    {
        return $this->belongsToMany(Photo::class, 'illustrer', 'numtype', 'numfichierphoto');
    }

    /**
     * Relation avec les points forts via la table posseder3
     */
    public function pointforts()
    {
        return $this->belongsToMany(PointFort::class, 'posseder3', 'numtype', 'numpointfort');
    }

    /**
     * Relation avec les commodités via la table posseder4
     */
    public function commodites()
    {
        return $this->belongsToMany(Commodite::class, 'posseder4', 'numtype', 'numcommodite');
    }
}

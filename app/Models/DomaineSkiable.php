<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DomaineSkiable extends Model
{
    use HasFactory;

    protected $table = 'domaineskiable';
    protected $primaryKey = 'numdomaine';
    public $timestamps = false;

    protected $fillable = [
        'numresort',
        'nomdomaine',
        'nomstation',
        'altitudeclub',
        'altitudestation',
        'longueurpiste',
        'nbpiste',
        'skiaupied',
        'descriptiondomaine'
    ];

    protected $casts = [
        'skiaupied' => 'boolean',
        'altitudeclub' => 'decimal:2',
        'altitudestation' => 'decimal:2',
        'longueurpiste' => 'decimal:2'
    ];

    /**
     * Relation avec le resort
     */
    public function resort()
    {
        return $this->belongsTo(Resort::class, 'numresort', 'numresort');
    }

    /**
     * Relation avec les pistes
     */
    public function pistes()
    {
        return $this->hasMany(Piste::class, 'numdomaine', 'numdomaine');
    }
}

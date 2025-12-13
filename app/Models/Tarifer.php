<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarifer extends Model
{
    use HasFactory;

    protected $table = 'tarifer';
    public $incrementing = false;
    protected $primaryKey = null; 
    public $timestamps = false;

    protected $fillable = [
        'numtype',
        'numresort',
        'numperiode',
        'prix',
        'prix_promo',
    ];

    public function typeChambre()
    {
        return $this->belongsTo(TypeChambre::class, 'numtype', 'numtype');
    }

    public function periode()
    {
        return $this->belongsTo(Periode::class, 'numperiode', 'numperiode');
    }

    public function resort()
    {
        return $this->belongsTo(Resort::class, 'numresort', 'numresort');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    protected $table = 'restaurant';
    protected $primaryKey = 'numrestaurant';
    public $timestamps = false;

    protected $fillable = [
        'numresort',
        'nomrestaurant',
        'typerestaurant',
        'descriptionrestaurant'
    ];

    /**
     * Relation avec le resort
     */
    public function resort()
    {
        return $this->belongsTo(Resort::class, 'numresort', 'numresort');
    }
}

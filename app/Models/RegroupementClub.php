<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegroupementClub extends Model
{
    use HasFactory;

    protected $table = 'regroupementclub';
    protected $primaryKey = 'numregroupement';
    public $timestamps = false;

    protected $fillable = [
        'nomregroupement',
        'descregroupement',
    ];
    
    public function resorts()
    {
        return $this->belongsToMany(Resort::class, 'appartenir', 'numregroupement', 'numresort');
    }
}
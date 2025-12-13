<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Periode extends Model
{
    use HasFactory;

    protected $table = 'periode';
    protected $primaryKey = 'numperiode';
    public $timestamps = false;

    protected $fillable = [
        'nomperiode',
        'datedebutperiode',
        'datefinperiode',
    ];
}
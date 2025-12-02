<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Typeactivite extends Model
{
    protected $table = 'typeactivite';

    protected $primaryKey = 'numtypeactivite';

    public $timestamps = false;

    protected $fillable = [
        'nomtypeactivite', 
        'desctypeactivite',
        'nbactiviteincluses',
        'nbactivitealacarte'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partenaire extends Model
{
    protected $table = 'partenaire';

    protected $primaryKey = 'numpartenaire';

    public $timestamps = false;

    protected $fillable = [
        'nompartenaire', 
        'emailpartenaire'
    ];
}

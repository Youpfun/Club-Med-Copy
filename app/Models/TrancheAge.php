<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrancheAge extends Model
{
    protected $table = 'trancheage';

    protected $primaryKey = 'numtrancheage';

    public $timestamps = false;

    protected $fillable = [
        'libelletrancheage', 
        'agemaxtroncheage', 
        'agemintroncheage'
    ];
}

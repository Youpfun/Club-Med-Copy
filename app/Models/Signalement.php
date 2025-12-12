<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signalement extends Model
{
    use HasFactory;

    protected $table = 'signalements';
    protected $primaryKey = 'numsignalement';
    public $timestamps = false;

    protected $fillable = [
        'numresort',
        'numavis',
        'user_id',
        'message',
        'datesignalement',
        'traite'
    ];

    protected $casts = [
        'datesignalement' => 'datetime',
        'traite' => 'boolean',
    ];

    public function resort() {
        return $this->belongsTo(Resort::class, 'numresort', 'numresort');
    }

    public function avis() {
        return $this->belongsTo(Avis::class, 'numavis', 'numavis');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}

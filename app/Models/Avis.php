<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avis extends Model
{
    use HasFactory;

    protected $table = 'avis';
    protected $primaryKey = 'numavis';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'numresort',
        'noteavis',
        'commentaire',
        'datepublication',
        'reponse',
        'reponse_user_id',
        'date_reponse'
    ];

    protected $casts = [
        'date_reponse' => 'datetime',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function resort() {
        return $this->belongsTo(Resort::class, 'numresort', 'numresort');
    }

    public function photos() {
        return $this->hasMany(Photo::class, 'numavis', 'numavis');
    }

    public function repondeur() {
        return $this->belongsTo(User::class, 'reponse_user_id');
    }

    public function hasReponse() {
        return !empty($this->reponse);
    }
}
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
        'datepublication'
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
}
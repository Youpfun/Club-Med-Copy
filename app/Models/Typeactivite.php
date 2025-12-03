<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeActivite extends Model
{
    protected $table = 'typeactivite';
    protected $primaryKey = 'numtypeactivite';
    public $timestamps = false;

    protected $fillable = ['nomtypeactivite', 'desctypeactivite', 'nbactiviteincluses', 'nbactivitealacarte'];

    public function activites()
    {
        return $this->hasMany(Activite::class, 'numtypeactivite', 'numtypeactivite');
    }

    public function resorts()
    {
        return $this->belongsToMany(Resort::class, 'partager', 'numtypeactivite', 'numresort');
    }
}

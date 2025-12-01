<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $table = 'photo';
    protected $primaryKey = 'numfichierphoto';
    public $timestamps = false;

    protected $fillable = ['numresort', 'nomfichierphoto', 'cheminfichierphoto', 'formatphoto', 'taillephoto'];

    public function resort()
    {
        return $this->belongsTo(Resort::class, 'numresort', 'numresort');
    }
}

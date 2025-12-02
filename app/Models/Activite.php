<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\TypeActivite;
use App\Models\ActiviteALaCarte;
use App\Models\TrancheAge;
use App\Models\Partenaire;

class Activite extends Model
{
    protected $table = 'activite';

    protected $primaryKey = 'numactivite';

    public $timestamps = false;

    protected $fillable = [
        'numtypeactivite', 
        'nomactivite', 
        'dureeactivite', 
        'descriptionactivite', 
        'agemin', 
        'frequence', 
        'estincluse'
    ];
    public function typeActivite()
    {
        return $this->belongsTo(TypeActivite::class, 'numtypeactivite', 'numtypeactivite');
    }
    public function aLaCarte()
    {
        return $this->hasOne(ActiviteALaCarte::class, 'numactivite', 'numactivite');
    }
    public function tranchesAge()
    {
        return $this->belongsToMany(TrancheAge::class, 'cibler', 'numactivite', 'numtrancheage');
    }
    public function partenaires()
    {
        return $this->belongsToMany(Partenaire::class, 'fourni', 'numactivite', 'numpartenaire');
    }
    public static function activiteComplete($numactivite) 
    {
        return self::with(['typeActivite', 'aLaCarte', 'tranchesAge'])->find($numactivite);
    }
}

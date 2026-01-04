<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activite extends Model
{
    use HasFactory;

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
        'estincluse',
        'imageactivite'
    ];

    /**
     * Relation vers le Type d'Activité (Clé étrangère obligatoire)
     */
    public function typeActivite()
    {
        return $this->belongsTo(TypeActivite::class, 'numtypeactivite', 'numtypeactivite');
    }

    /**
     * Relation vers ActiviteALaCarte (Optionnel)
     */
    public function aLaCarte()
    {
        return $this->hasOne(ActiviteALaCarte::class, 'numactivite', 'numactivite');
    }

    /**
     * Relation ManyToMany vers TrancheAge
     */
    public function tranchesAge()
    {
        return $this->belongsToMany(TrancheAge::class, 'cibler', 'numactivite', 'numtrancheage');
    }

    /**
     * Relation ManyToMany vers Partenaire
     */
    public function partenaires()
    {
        return $this->belongsToMany(Partenaire::class, 'fourni', 'numactivite', 'numpartenaire');
    }

    /**
     * Helper pour charger toutes les relations d'un coup
     */
    public static function activiteComplete($numactivite) 
    {
        return self::with(['typeActivite', 'aLaCarte', 'tranchesAge'])->find($numactivite);
    }
}
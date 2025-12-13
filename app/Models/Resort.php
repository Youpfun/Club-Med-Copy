<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Assurez-vous que les modèles importés existent
use App\Models\Pays;
use App\Models\Documentation;
use App\Models\Avis;
use App\Models\TypeActivite;
use App\Models\TypeChambre;
use App\Models\DomaineSkiable;
use App\Models\Restaurant;
use App\Models\Photo;
use App\Models\RegroupementClub; 
use App\Models\Typeclub;
use App\Models\Localisation;

class Resort extends Model
{
    use HasFactory;

    protected $table = 'resort';
    protected $primaryKey = 'numresort';
    public $timestamps = false;

    protected $fillable = [
        'codepays', 'numdomaine', 'numdocumentation', 'nomresort', 
        'descriptionresort', 'nbchambrestotal', 'nbtridents', 
        'latituderesort', 'longituderesort'
    ];

    /**
     * Attribut calculé pour la moyenne des avis.
     */
    public function getMoyenneavisAttribute()
    {
        $avisCollection = $this->relationLoaded('avis') ? $this->avis : $this->avis()->get();
        
        if ($avisCollection->isEmpty()) {
            return null;
        }

        return round($avisCollection->avg('noteavis'), 1);
    }

    // --- RELATIONS ---

    public function pays() { return $this->belongsTo(Pays::class, 'codepays', 'codepays'); }
    public function documentation() { return $this->belongsTo(Documentation::class, 'numdocumentation', 'numdocumentation'); }
    public function avis() { return $this->hasMany(Avis::class, 'numresort', 'numresort'); }
    public function typeclubs() { return $this->belongsToMany(Typeclub::class, 'classer', 'numresort', 'numtypeclub'); }
    public function localisations() { return $this->belongsToMany(Localisation::class, 'situer2', 'numresort', 'numlocalisation'); }
    public function photos() { return $this->hasMany(Photo::class, 'numresort', 'numresort'); }
    public function typesActivites() { return $this->belongsToMany(TypeActivite::class, 'partager', 'numresort', 'numtypeactivite'); }
    public function typechambres() { return $this->belongsToMany(TypeChambre::class, 'proposer', 'numresort', 'numtype'); }
    public function domaineskiable() { return $this->hasOne(DomaineSkiable::class, 'numresort', 'numresort'); }
    public function restaurants() { return $this->hasMany(Restaurant::class, 'numresort', 'numresort'); }
    public function regroupements() { return $this->belongsToMany(RegroupementClub::class, 'appartenir', 'numresort', 'numregroupement'); }
}
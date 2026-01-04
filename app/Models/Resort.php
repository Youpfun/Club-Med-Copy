<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function getMoyenneavisAttribute()
    {
        $avisCollection = $this->relationLoaded('avis') ? $this->avis : $this->avis()->get();
        return $avisCollection->isEmpty() ? null : round($avisCollection->avg('noteavis'), 1);
    }

    public function pays() { return $this->belongsTo(Pays::class, 'codepays', 'codepays'); }
    
    public function domaineskiable() { return $this->belongsTo(DomaineSkiable::class, 'numdomaine', 'numdomaine'); }
    
    public function documentation() { return $this->belongsTo(Documentation::class, 'numdocumentation', 'numdocumentation'); }

    public function photos() { return $this->hasMany(Photo::class, 'numresort', 'numresort'); }
    
    public function restaurants() { return $this->hasMany(Restaurant::class, 'numresort', 'numresort'); }

    public function regroupements() { 
        return $this->belongsToMany(RegroupementClub::class, 'appartenir', 'numresort', 'numregroupement'); 
    }

    public function typechambres() { 
        return $this->belongsToMany(TypeChambre::class, 'proposer', 'numresort', 'numtype')
                    ->withPivot('nbchambres'); 
    }

    public function tarifs() {
        return $this->hasMany(Tarifer::class, 'numresort', 'numresort');
    }
    
    public function avis() { return $this->hasMany(Avis::class, 'numresort', 'numresort'); }
    public function typeclubs() { return $this->belongsToMany(Typeclub::class, 'classer', 'numresort', 'numtypeclub'); }
    public function localisations() { return $this->belongsToMany(Localisation::class, 'situer2', 'numresort', 'numlocalisation'); }
    public function typesActivites() { return $this->belongsToMany(TypeActivite::class, 'partager', 'numresort', 'numtypeactivite'); }
}
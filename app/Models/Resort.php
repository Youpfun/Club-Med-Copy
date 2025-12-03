<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Pays;
use App\Models\Documentation;
use App\Models\Avis;
use App\Models\Localisation;

class Resort extends Model
{
	protected $table = 'resort';

	protected $primaryKey = 'numresort';

	public $timestamps = false;

	protected $fillable =[
		'codepays', 'numdomaine', 'numdocumentation', 'nomresort', 'descriptionresort', 'moyenneavis', 'nbchambrestotal', 'nbtridents'];

	/**
	 * Calcul dynamique de la moyenne des avis à partir de la table "avis".
	 * On surcharge l'attribut "moyenneavis" pour qu'il reflète toujours
	 * la moyenne réelle des notes (noteavis) en base.
	 */
	public function getMoyenneavisAttribute()
	{
		// Si la relation "avis" est déjà chargée, on utilise la collection en mémoire
		$avisCollection = $this->relationLoaded('avis')
			? $this->avis
			: $this->avis()->get();

		if ($avisCollection->isEmpty()) {
			return null;
		}

		// Moyenne arrondie à 1 décimale
		return round($avisCollection->avg('noteavis'), 1);
	}

		
	public function pays()
	{
			return $this->belongsTo(Pays::class, 'codepays', 'codepays');
	}

	public function documentation()
    {
        return $this->belongsTo(Documentation::class, 'numdocumentation', 'numdocumentation');
    }

	public function avis()
    {
        return $this->hasMany(Avis::class, 'numresort', 'numresort');
    }

	public static function resortPaysDocumentationAvis($numresort) 
	{
		return self::with(['pays', 'documentation', 'avis' => function($query) 
		{$query->orderBy('datepublication', 'desc')->take(3);
		}])->find($numresort);
	}

	public static function resortPaysAvis($numresort) 
	{
		return self::with(['pays', 'avis' => function($query) 
		{$query->orderBy('datepublication', 'desc')->take(3);
		}])->find($numresort);
	}

	public function typeclubs()
	{
		return $this->belongsToMany('App\Models\Typeclub', 'classer', 'numresort', 'numtypeclub');
	}

	public function localisations()
	{
		return $this->belongsToMany('App\Models\Localisation', 'situer2', 'numresort', 'numlocalisation');
	}
    public function photos()
    {
        return $this->hasMany(Photo::class, 'numresort', 'numresort');
    }
	public function typesActivites()
    {
        return $this->belongsToMany(TypeActivite::class, 'partager', 'numresort', 'numtypeactivite');
    }

	/**
	 * Relation avec les types de chambres via la table proposer
	 */
	public function typechambres()
	{
		return $this->belongsToMany(TypeChambre::class, 'proposer', 'numresort', 'numtype');
	}
}

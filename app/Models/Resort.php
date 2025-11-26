<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Pays;
use App\Models\Documentation;
use App\Models\Avis;

class Resort extends Model
{
	protected $table = 'resort';

	protected $primaryKey = 'numresort';

	public $timestamps = false;

	protected $fillable =[
		'codepays', 'numdomaine', 'numdocumentation', 'nomresort', 'descriptionresort', 'moyenneavis', 'nbchambrestotal', 'nbtridents'];

		
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

	public function typeclubs()
	{
		return $this->belongsToMany('App\Models\Typeclub', 'classer', 'numresort', 'numtypeclub');
	}
}

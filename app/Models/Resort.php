<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resort extends Model
{
	protected $table = 'resort';

	protected $primaryKey = 'numresort';

	public $timestamps = false;

	protected $fillable =[
		'codepays', 'numdomaine', 'numdocumentation', 'nomresort', 'descriptionresort', 'moyenneavis', 'nbchambrestotal', 'nbtridents'];

	public function typeclubs()
	{
		return $this->belongsToMany('App\Models\Typeclub', 'classer', 'numresort', 'numtypeclub');
	}
}

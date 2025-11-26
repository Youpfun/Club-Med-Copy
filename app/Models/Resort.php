<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resort extends Model
{
	protected $table = 'resort';

	protected $primaryKey = 'numresort';

	protected $fillable =[
		'codepays', 'numdomaine', 'numdocumentation', 'nomresort', 'descriptionresort', 'moyenneavis', 'nbchambrestotal', 'nbtridents'];
}

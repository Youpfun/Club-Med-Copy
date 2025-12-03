<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avis extends Model
{
    use HasFactory;

    protected $table = 'avis';

	protected $primaryKey = 'numavis';

	public $timestamps = false;

	protected $fillable =['id', 'numresort', 'noteavis', 'commentaire', 'datepublication'];
}

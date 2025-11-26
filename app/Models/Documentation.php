<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documentation extends Model
{
    use HasFactory;

    protected $table = 'documentation';

	protected $primaryKey = 'numdocumentation';

	public $timestamps = false;

	protected $fillable =['numresort', 'titredoc', 'url', 'formatdoc'];
}

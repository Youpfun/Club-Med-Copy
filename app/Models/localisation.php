<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class localisation extends Model
{
    protected $table = 'localisation';

    protected $primaryKey = 'numlocalisation';

    public $timestamps = false;

    protected $fillable = [
	'nomlocalisation'];
}

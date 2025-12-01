<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Localisation extends Model
{
    protected $table = 'localisation';

    protected $primaryKey = 'numlocalisation';

    public $timestamps = false;

    protected $fillable = [
	'nomlocalisation'];

	public function resorts()
	{
		return $this->belongsToMany('App\Models\Resort', 'situer2', 'numlocalisation', 'numresort');
	}
}

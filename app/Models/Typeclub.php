<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Typeclub extends Model
{
    protected $table = 'typeclub';

    protected $primaryKey = 'numtypeclub';

    public $timestamps = false;

    protected $fillable = [
	'nomtypeclub'];
}

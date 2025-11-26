<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'client';

    protected $primaryKey = 'numclient';

    public $timestamps = false;

    protected $fillable = [
	'nomclient', 'prenomclient', 'emailclient', 'login', 'password', 'ville', 'numrue', 'nomrue', 'codepostal', 'telephone', 'genreclient', 'datenaissance'];

}

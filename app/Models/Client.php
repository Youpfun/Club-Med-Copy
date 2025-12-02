<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Client extends Authenticatable
{
    protected $table = 'client';

    protected $primaryKey = 'numclient';

    public $timestamps = false;

    protected $fillable = [
        'nomclient', 
        'prenomclient', 
        'emailclient', 
        'login', 
        'password', 
        'ville', 
        'numrue', 
        'nomrue', 
        'codepostal', 
        'telephone', 
        'genreclient', 
        'datenaissance'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
        'datenaissance' => 'date',
    ];

    public function getEmailForPasswordReset()
    {
        return $this->emailclient;
    }
}
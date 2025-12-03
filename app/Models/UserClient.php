<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Client extends Authenticatable
{
    /*protected $table = 'users';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'genre',
        'datenaissance',
        'telephone',
        'numrue',
        'nomrue',
        'codepostal',
        'ville',
        'idcarte'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'datenaissance' => 'date',
        'email_verified_at' => 'datetime',
    ];

    public function getEmailForPasswordReset()
    {
        return $this->email;
    }*/
}
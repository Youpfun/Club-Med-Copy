<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemandeDisponibilite extends Model
{
    protected $table = 'demande_disponibilite';
    protected $primaryKey = 'numdemande';

    protected $fillable = [
        'numresort',
        'user_id',
        'date_debut',
        'date_fin',
        'nb_chambres',
        'types_chambres',
        'nb_personnes',
        'message',
        'statut',
        'response_message',
        'response_status',
        'response_details',
        'responded_at',
        'validation_token',
        'validation_token_expires_at',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'responded_at' => 'datetime',
        'validation_token_expires_at' => 'datetime',
        'response_details' => 'array',
    ];

    public function resort()
    {
        return $this->belongsTo(Resort::class, 'numresort', 'numresort');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function isPending()
    {
        return $this->statut === 'pending';
    }

    public function isAvailable()
    {
        return $this->response_status === 'available';
    }
}

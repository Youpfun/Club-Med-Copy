<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProspectionResort extends Model
{
    use HasFactory;

    protected $table = 'prospection_resort';
    protected $primaryKey = 'numprospection';

    protected $fillable = [
        'user_id',
        'nom_resort',
        'email_resort',
        'pays',
        'ville',
        'telephone',
        'objet',
        'message',
        'statut',
        'reponse',
        'date_reponse',
    ];

    protected $casts = [
        'date_reponse' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Utilisateur qui a créé la prospection
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Statuts avec labels
     */
    public static function getStatuts()
    {
        return [
            'envoyee' => 'Envoyée',
            'repondue' => 'Répondue',
            'en_cours' => 'En cours de traitement',
            'cloturee' => 'Clôturée',
        ];
    }

    /**
     * Label du statut
     */
    public function getStatutLabelAttribute()
    {
        return self::getStatuts()[$this->statut] ?? $this->statut;
    }

    /**
     * Couleur du statut
     */
    public function getStatutColorAttribute()
    {
        return match($this->statut) {
            'envoyee' => 'blue',
            'repondue' => 'green',
            'en_cours' => 'yellow',
            'cloturee' => 'gray',
            default => 'gray',
        };
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProspectionPartenaire extends Model
{
    use HasFactory;

    protected $table = 'prospection_partenaire';
    protected $primaryKey = 'numprospection';

    protected $fillable = [
        'user_id',
        'nom_partenaire',
        'email_partenaire',
        'type_activite',
        'pays',
        'ville',
        'telephone',
        'site_web',
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
     * Types d'activités possibles
     */
    public static function getTypesActivite()
    {
        return [
            'ski' => '⛷️ Ski / Sports d\'hiver',
            'plongee' => '🤿 Plongée sous-marine',
            'spa' => '💆 Spa & Bien-être',
            'golf' => '⛳ Golf',
            'nautique' => '🚤 Sports nautiques',
            'equitation' => '🐴 Équitation',
            'escalade' => '🧗 Escalade / Montagne',
            'excursion' => '🚐 Excursions / Visites',
            'gastronomie' => '🍽️ Gastronomie / Cours de cuisine',
            'yoga' => '🧘 Yoga / Méditation',
            'enfants' => '👶 Activités enfants',
            'autre' => '📋 Autre',
        ];
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
     * Label du type d'activité
     */
    public function getTypeActiviteLabelAttribute()
    {
        return self::getTypesActivite()[$this->type_activite] ?? $this->type_activite;
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

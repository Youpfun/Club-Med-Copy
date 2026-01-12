<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjetSejour extends Model
{
    use HasFactory;

    protected $table = 'projet_sejour';
    protected $primaryKey = 'numprojet';

    protected $fillable = [
        'user_id',
        'nom_sejour',
        'description',
        'pays',
        'ville',
        'nb_tridents',
        'date_debut_prevue',
        'date_fin_prevue',
        'prospection_resort_id',
        'prospection_partenaires_ids',
        'budget_estime',
        'capacite_prevue',
        'activites_prevues',
        'points_forts',
        'statut',
        'directeur_id',
        'commentaire_directeur',
        'date_soumission',
        'date_decision',
        'numresort',
    ];

    protected $casts = [
        'prospection_partenaires_ids' => 'array',
        'date_debut_prevue' => 'date',
        'date_fin_prevue' => 'date',
        'date_soumission' => 'datetime',
        'date_decision' => 'datetime',
        'budget_estime' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // =============================
    // RELATIONS
    // =============================

    /**
     * Créateur du projet (membre marketing)
     */
    public function createur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Directeur qui a traité le projet
     */
    public function directeur()
    {
        return $this->belongsTo(User::class, 'directeur_id');
    }

    /**
     * Prospection resort liée
     */
    public function prospectionResort()
    {
        return $this->belongsTo(ProspectionResort::class, 'prospection_resort_id', 'numprospection');
    }

    /**
     * Resort créé (si le projet est approuvé et le resort créé)
     */
    public function resort()
    {
        return $this->belongsTo(Resort::class, 'numresort', 'numresort');
    }

    /**
     * Récupère les prospections partenaires liées
     */
    public function getProspectionsPartenairesAttribute()
    {
        if (empty($this->prospection_partenaires_ids)) {
            return collect();
        }
        return ProspectionPartenaire::whereIn('numprospection', $this->prospection_partenaires_ids)->get();
    }

    // =============================
    // STATUTS
    // =============================

    public static function getStatuts()
    {
        return [
            'brouillon' => 'Brouillon',
            'soumis' => 'Soumis au Directeur',
            'en_revision' => 'En révision',
            'approuve' => 'Approuvé',
            'refuse' => 'Refusé',
            'en_creation' => 'En cours de création',
        ];
    }

    public function getStatutLabelAttribute()
    {
        return self::getStatuts()[$this->statut] ?? $this->statut;
    }

    public function getStatutColorAttribute()
    {
        return match($this->statut) {
            'brouillon' => 'gray',
            'soumis' => 'blue',
            'en_revision' => 'yellow',
            'approuve' => 'green',
            'refuse' => 'red',
            'en_creation' => 'purple',
            default => 'gray',
        };
    }

    public function getStatutIconAttribute()
    {
        return match($this->statut) {
            'brouillon' => '📝',
            'soumis' => '📤',
            'en_revision' => '🔄',
            'approuve' => '✅',
            'refuse' => '❌',
            'en_creation' => '🏗️',
            default => '📋',
        };
    }

    // =============================
    // SCOPES
    // =============================

    public function scopeBrouillon($query)
    {
        return $query->where('statut', 'brouillon');
    }

    public function scopeSoumis($query)
    {
        return $query->where('statut', 'soumis');
    }

    public function scopeApprouve($query)
    {
        return $query->where('statut', 'approuve');
    }

    public function scopeEnAttente($query)
    {
        return $query->whereIn('statut', ['soumis', 'en_revision']);
    }

    // =============================
    // HELPERS
    // =============================

    public function canBeEdited(): bool
    {
        return in_array($this->statut, ['brouillon', 'en_revision']);
    }

    public function canBeSubmitted(): bool
    {
        return in_array($this->statut, ['brouillon', 'en_revision']);
    }

    public function canBeReviewed(): bool
    {
        return $this->statut === 'soumis';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Remboursement extends Model
{
    use HasFactory;

    protected $table = 'remboursement';
    protected $primaryKey = 'numremboursement';

    protected $fillable = [
        'numreservation',
        'user_id',
        'montant',
        'statut',
        'raison',
        'notes',
        'stripe_refund_id',
        'methode_remboursement',
        'date_demande',
        'date_traitement',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_demande' => 'datetime',
        'date_traitement' => 'datetime',
    ];

    /**
     * Statuts possibles
     */
    const STATUT_EN_ATTENTE = 'en_attente';
    const STATUT_TRAITE = 'traite';
    const STATUT_REFUSE = 'refuse';
    const STATUT_REMBOURSE = 'rembourse';

    /**
     * Relation avec la réservation
     */
    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'numreservation', 'numreservation');
    }

    /**
     * Relation avec l'utilisateur qui a initié le remboursement
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Scope pour les remboursements en attente
     */
    public function scopeEnAttente($query)
    {
        return $query->where('statut', self::STATUT_EN_ATTENTE);
    }

    /**
     * Scope pour les remboursements traités
     */
    public function scopeTraites($query)
    {
        return $query->where('statut', self::STATUT_TRAITE);
    }

    /**
     * Scope pour les remboursements remboursés
     */
    public function scopeRembourses($query)
    {
        return $query->where('statut', self::STATUT_REMBOURSE);
    }

    /**
     * Vérifier si le remboursement est en attente
     */
    public function estEnAttente(): bool
    {
        return $this->statut === self::STATUT_EN_ATTENTE;
    }

    /**
     * Vérifier si le remboursement est effectué
     */
    public function estRembourse(): bool
    {
        return $this->statut === self::STATUT_REMBOURSE;
    }

    /**
     * Marquer comme remboursé
     */
    public function marquerRembourse(?string $stripeRefundId = null): void
    {
        $this->update([
            'statut' => self::STATUT_REMBOURSE,
            'stripe_refund_id' => $stripeRefundId,
            'date_traitement' => now(),
        ]);
    }
}

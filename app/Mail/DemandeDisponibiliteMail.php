<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\DemandeDisponibilite;

class DemandeDisponibiliteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $demande;
    public $tokenLink;

    public function __construct(DemandeDisponibilite $demande, string $tokenLink)
    {
        $this->demande = $demande;
        $this->tokenLink = $tokenLink;
    }

    public function build()
    {
        return $this->subject("Demande de disponibilité - {$this->demande->date_debut->format('d/m/Y')} au {$this->demande->date_fin->format('d/m/Y')}")
            ->view('emails.demande_disponibilite')
            ->with([
                'demande' => $this->demande,
                'tokenLink' => $this->tokenLink,
            ]);
    }
}

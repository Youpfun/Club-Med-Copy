<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Reservation;

class PartnerValidationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;
    public $partenaire;
    public $tokenLink;

    public function __construct(Reservation $reservation, $partenaire, string $tokenLink)
    {
        $this->reservation = $reservation;
        $this->partenaire = $partenaire;
        $this->tokenLink = $tokenLink;
    }

    public function build()
    {
        return $this->subject("Validation des dates - Réservation #{$this->reservation->numreservation}")
            ->view('emails.partner_validation')
            ->with([
                'reservation' => $this->reservation,
                'partenaire' => $this->partenaire,
                'tokenLink' => $this->tokenLink,
            ]);
    }
}

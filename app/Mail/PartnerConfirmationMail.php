<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Reservation;

class PartnerConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;
    public $partenaire;

    public function __construct(Reservation $reservation, $partenaire)
    {
        $this->reservation = $reservation;
        $this->partenaire = $partenaire;
    }

    public function build()
    {
        return $this->subject("Confirmation finale - Réservation #{$this->reservation->numreservation}")
            ->view('emails.partner_confirmation')
            ->with([
                'reservation' => $this->reservation,
                'partenaire' => $this->partenaire,
            ]);
    }
}

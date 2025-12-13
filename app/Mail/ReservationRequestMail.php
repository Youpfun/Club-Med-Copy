<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Reservation;

class ReservationRequestMail extends Mailable
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
        return $this->subject("Demande de confirmation de dates - Réservation #{$this->reservation->numreservation}")
                    ->view('emails.reservation_request')
                    ->with([
                        'reservation' => $this->reservation,
                        'partenaire' => $this->partenaire,
                    ]);
    }
}
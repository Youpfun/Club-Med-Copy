<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Reservation;

class ResortValidationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;
    public $resort;
    public $tokenLink;

    public function __construct(Reservation $reservation, $resort, $tokenLink)
    {
        $this->reservation = $reservation;
        $this->resort = $resort;
        $this->tokenLink = $tokenLink;
    }

    public function build()
    {
        return $this->subject("Nouvelle réservation à valider - #{$this->reservation->numreservation}")
            ->view('emails.resort_validation')
            ->with([
                'reservation' => $this->reservation,
                'resort' => $this->resort,
                'tokenLink' => $this->tokenLink,
            ]);
    }
}

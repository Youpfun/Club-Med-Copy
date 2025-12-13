<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Reservation;

class ResortConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;

    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    public function build()
    {
        return $this->subject("Confirmation de réservation - #{$this->reservation->numreservation}")
            ->view('emails.resort_confirmation')
            ->with([
                'reservation' => $this->reservation,
            ]);
    }
}

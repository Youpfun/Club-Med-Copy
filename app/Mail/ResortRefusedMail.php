<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Reservation;

class ResortRefusedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;
    public $resort;
    public $comment;

    public function __construct(Reservation $reservation, $resort, $comment = null)
    {
        $this->reservation = $reservation;
        $this->resort = $resort;
        $this->comment = $comment;
    }

    public function build()
    {
        return $this->subject("Mise à jour de votre réservation #{$this->reservation->numreservation}")
            ->view('emails.resort_refused')
            ->with([
                'reservation' => $this->reservation,
                'resort' => $this->resort,
                'comment' => $this->comment,
            ]);
    }
}

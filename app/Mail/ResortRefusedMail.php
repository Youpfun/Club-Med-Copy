<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Reservation;
use App\Models\Resort;

class ResortRefusedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;
    public $resort;
    public $comment;
    public $alternativeResorts;

    public function __construct(Reservation $reservation, $resort, $comment = null, $alternativeResorts = null)
    {
        $this->reservation = $reservation;
        $this->resort = $resort;
        $this->comment = $comment;
        $this->alternativeResorts = $alternativeResorts ?? collect();
    }

    public function build()
    {
        return $this->subject("Mise à jour de votre réservation #{$this->reservation->numreservation}")
            ->view('emails.resort_refused')
            ->with([
                'reservation' => $this->reservation,
                'resort' => $this->resort,
                'comment' => $this->comment,
                'alternativeResorts' => $this->alternativeResorts,
            ]);
    }
}

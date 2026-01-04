<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Reservation;
use App\Models\Resort;

class AlternativeResortProposalMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;
    public $originalResort;
    public $alternativeResort;
    public $tokenLink;
    public $message;

    public function __construct(Reservation $reservation, Resort $originalResort, Resort $alternativeResort, $tokenLink, $message = null)
    {
        $this->reservation = $reservation;
        $this->originalResort = $originalResort;
        $this->alternativeResort = $alternativeResort;
        $this->tokenLink = $tokenLink;
        $this->message = $message;
    }

    public function build()
    {
        return $this->subject("Proposition de Resort Alternatif - Réservation #{$this->reservation->numreservation}")
            ->view('emails.alternative_resort_proposal')
            ->with([
                'reservation' => $this->reservation,
                'originalResort' => $this->originalResort,
                'alternativeResort' => $this->alternativeResort,
                'tokenLink' => $this->tokenLink,
                'message' => $this->message,
            ]);
    }
}

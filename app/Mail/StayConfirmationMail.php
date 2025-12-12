<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Reservation;

class StayConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;
    public $recipientType;

    public function __construct(Reservation $reservation, $recipientType = 'resort')
    {
        $this->reservation = $reservation;
        $this->recipientType = $recipientType;
    }

    public function build()
    {
        $subject = "Confirmation de séjour - Réservation #{$this->reservation->numreservation}";
        
        if ($this->recipientType === 'partenaire') {
            $subject = "Confirmation d'activités partenaires - Réservation #{$this->reservation->numreservation}";
        }

        return $this->subject($subject)
                    ->view('emails.stay_confirmation')
                    ->with([
                        'reservation' => $this->reservation,
                        'recipientType' => $this->recipientType,
                    ]);
    }
}

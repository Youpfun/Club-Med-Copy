<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Reservation;

class ReservationRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;
    public $reason;
    public $reasonLabel;
    public $alternativeResorts;

    public function __construct(Reservation $reservation, $reason, $alternativeResorts = null)
    {
        $this->reservation = $reservation;
        $this->reason = $reason;
        $this->reasonLabel = $this->getReasonLabel($reason);
        $this->alternativeResorts = $alternativeResorts ?? collect();
    }

    private function getReasonLabel($reason)
    {
        $labels = [
            'client_refused' => 'Le client a refusé les alternatives proposées',
            'new_resort_not_accepted' => 'Le nouveau resort n\'a pas pu accepter la réservation',
            'availability_issue' => 'Problème de disponibilité',
            'other' => 'Autre raison',
        ];
        return $labels[$reason] ?? 'Raison non spécifiée';
    }

    public function build()
    {
        return $this->subject("Annulation de votre réservation #{$this->reservation->numreservation}")
            ->view('emails.reservation_rejected')
            ->with([
                'reservation' => $this->reservation,
                'reason' => $this->reason,
                'reasonLabel' => $this->reasonLabel,
                'alternativeResorts' => $this->alternativeResorts,
            ]);
    }
}

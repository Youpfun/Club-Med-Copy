<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Reservation;

class ActivityCancelledMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;
    public $cancelledActivities;
    public $totalRefund;
    public $isSingleActivity;
    public $cancellationReason;

    /**
     * @param Reservation $reservation
     * @param array $cancelledActivities - Liste des activités annulées avec leurs détails
     * @param float $totalRefund - Montant total remboursé/déduit
     * @param bool $isSingleActivity - True si une seule activité, false si toutes
     * @param string $cancellationReason - Raison de l'annulation (default, partner_no_response)
     */
    public function __construct(Reservation $reservation, array $cancelledActivities, float $totalRefund, bool $isSingleActivity = true, string $cancellationReason = 'default')
    {
        $this->reservation = $reservation;
        $this->cancelledActivities = $cancelledActivities;
        $this->totalRefund = $totalRefund;
        $this->isSingleActivity = $isSingleActivity;
        $this->cancellationReason = $cancellationReason;
    }

    public function build()
    {
        if ($this->cancellationReason === 'partner_no_response') {
            $subject = "Annulation d'activité(s) - Partenaire non disponible - Réservation #{$this->reservation->numreservation}";
        } else {
            $subject = $this->isSingleActivity 
                ? "Annulation d'une activité - Réservation #{$this->reservation->numreservation}"
                : "Annulation de vos activités - Réservation #{$this->reservation->numreservation}";
        }

        return $this->subject($subject)
            ->view('emails.activity_cancelled')
            ->with([
                'reservation' => $this->reservation,
                'cancelledActivities' => $this->cancelledActivities,
                'totalRefund' => $this->totalRefund,
                'isSingleActivity' => $this->isSingleActivity,
                'cancellationReason' => $this->cancellationReason,
            ]);
    }
}

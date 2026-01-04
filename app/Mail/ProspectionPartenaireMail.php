<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\ProspectionPartenaire;

class ProspectionPartenaireMail extends Mailable
{
    use Queueable, SerializesModels;

    public $prospection;

    public function __construct(ProspectionPartenaire $prospection)
    {
        $this->prospection = $prospection;
    }

    public function build()
    {
        return $this->subject($this->prospection->objet)
            ->replyTo('clubmedsae@gmail.com', 'Club Méditerranée - Marketing')
            ->view('emails.prospection_partenaire')
            ->with([
                'prospection' => $this->prospection,
            ]);
    }
}

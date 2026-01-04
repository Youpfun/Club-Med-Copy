<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\ProspectionResort;

class ProspectionResortMail extends Mailable
{
    use Queueable, SerializesModels;

    public $prospection;

    public function __construct(ProspectionResort $prospection)
    {
        $this->prospection = $prospection;
    }

    public function build()
    {
        return $this->subject($this->prospection->objet)
            ->replyTo('clubmedsae@gmail.com', 'Club Méditerranée - Marketing')
            ->view('emails.prospection_resort')
            ->with([
                'prospection' => $this->prospection,
            ]);
    }
}

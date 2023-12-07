<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NoDueMail extends Mailable
{
    use Queueable, SerializesModels;

    public $approval;
    public $currentUser;

    public function __construct($approval, $currentUser)
    {
        $this->approval = $approval;
        $this->currentUser = $currentUser;
    }

    public function build()
    {
        return $this->markdown('emails.nodue-application')
            ->subject('No Due Application Submitted');
    }
}
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdvanceApplicationMail extends Mailable
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
        return $this->markdown('emails.advance-application')
            ->subject('Application Submitted');
    }
}
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeaveEncashmentApprovalMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $content;

    public function __construct($user, $content)
    {
        $this->user = $user;
        $this->content = $content;
    }

    public function build()
    {
        return $this->markdown('emails.encashment-approved')
            ->subject('Leave Application Approved');
    }
}
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NoDueSectionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $department;
    public $user;

    public function __construct($department, $user)
    {
        $this->department = $department;
        $this->user = $user;
    }

    public function build()
    {
        return $this->markdown('emails.noduesection-application')
            ->subject('No Due Application Submitted');
    }
}
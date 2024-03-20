<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEventNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $event_data;

    public function __construct($data)
    {
        $this->event_data = $data;
    }

    public function build()
    {
        $event_data = $this->event_data;
        return $this->view('emails.event_creation', compact('event_data'));
    }
}

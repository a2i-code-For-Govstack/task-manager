<?php 

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use MailerSend\Helpers\Builder\Variable;
use MailerSend\Helpers\Builder\Personalization;
use MailerSend\LaravelDriver\MailerSendTrait;

class EventNotificationEmail extends Mailable
{
    use Queueable, SerializesModels, MailerSendTrait;

    public $subject;
    public $messageBody;

    public function __construct($subject, $messageBody)
    {
        $this->subject = $subject;
        $this->messageBody = $messageBody;
    }

    public function build()
    {
        $to = Arr::get($this->to, '0.address');

        return $this->view('emails.notification')
            ->with([
                'messageBody' => $this->messageBody,
            ])
            ->subject($this->subject)
            ->mailersend(
                null, // Template ID (null if not using a pre-defined template)
                [
                    new Variable($to, ['name' => 'Recipient Name'])
                ]
            );
    }
}

<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventNotification extends Notification
{
    use Queueable;

    protected $eventDetails;

    public function __construct($eventDetails)
    {
        $this->eventDetails = $eventDetails;
    }

    // Add the via method to specify the notification channels
    public function via($notifiable)
    {
        return ['mail']; // You can add more channels here as needed
    }

    // Create the mail representation of the notification
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Event Update')
                    ->line('There has been an update to your event.')
                    ->action('View Event', url('/'))
                    ->line('Thank you for using our application!');
    }

    // Optionally, add other notification channels (e.g., database, SMS)
    // public function toDatabase($notifiable)
    // {
    //     return [
    //         'eventDetails' => $this->eventDetails,
    //     ];
    // }
}

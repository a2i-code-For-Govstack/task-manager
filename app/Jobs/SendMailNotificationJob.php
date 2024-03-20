<?php

namespace App\Jobs;

use App\Traits\ApiHeart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class SendMailNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ApiHeart;

    protected $notification_data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($notification_data)
    {
        $this->notification_data = $notification_data;
    }

    public function handle()
    {
        $notification_data = $this->notification_data;
        if ($notification_data['type'] == 'task_creation') {
            $task_data = $notification_data;
            $email_subject = 'Task "' . $notification_data['task_title'] . '" has been created';
            $body = view('emails.task_creation', compact('task_data'))->render();
        } else {
            $event_data = $notification_data;
            $email_subject = 'Event ' . $notification_data['event_title'] . ' has been created';
            $body = view('emails.event_creation', compact('event_data'))->render();
        }


        $mail_data = [
            "email_from" => $notification_data['sender_email'],
            "email_from_name" => $notification_data['sender_en'],
            "format" => "html",
            "template" => "universal",
            "email_subject" => $email_subject,
            "body" => $body,
            "layout" => "default",
            "type" => [],
            "transport" => "default",
            "attachments" => [],
            "mail_type" => "default",
            "action_type" => "others",
            "receivers" => [
                [
                    "officer" => $notification_data['receiver_en'],
                    "office_unit" => "",
                    "designation" => "",
                    "officer_email" => $notification_data['receiver_email'],
                ],
            ],
        ];
        $data = [
            'data' => json_encode($mail_data),
        ];

        $response = $this->initHttp()->post(config('fire_notification_constants.emailer_url'), $data)->json();
        if (!$response || $response['status'] != 200) {
            \Log::error('Error sending mail notification' . json_encode($response));
        }
    }
}

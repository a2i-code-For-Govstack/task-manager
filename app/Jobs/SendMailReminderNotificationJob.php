<?php

namespace App\Jobs;

use App\Mail\SendEventNotificationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendMailReminderNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        $info = new SendEventNotificationMail($this->notification_data);
        \Log::info("mail sent successfully - " . $this->notification_data['user_email']);
        return Mail::to($this->notification_data['user_email'])->send($info);
    }
}

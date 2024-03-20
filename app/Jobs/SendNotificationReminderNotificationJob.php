<?php

namespace App\Jobs;

use App\Services\NtfyServices;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNotificationReminderNotificationJob implements ShouldQueue
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
        $title = $this->notification_data['title'] ?? null;
        $message = $this->notification_data['message'] ?? '';
        $recipient = $this->notification_data['recipient'] ?? null;
        $click_action = 'javascript:;';

        if ($title && $recipient) {
            (new NtfyServices())->dispatchToNtfy($title, $message, $recipient, $click_action);
        }

    }
}

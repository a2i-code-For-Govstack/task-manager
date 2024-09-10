<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Google\Cloud\PubSub\PubSubClient;
use Illuminate\Support\Facades\Mail;
use App\Mail\CalendarEventNotification;

class PullPubSubMessages extends Command
{
    protected $signature = 'pubsub:pull';
    protected $description = 'Pull messages from Google Cloud Pub/Sub';

    public function handle()
    {
        $pubsub = new PubSubClient([
            'projectId' => config('services.google.project_id'),
            'keyFilePath' => config('services.google.credentials'),
        ]);

        $subscription = $pubsub->subscription(config('services.google.pubsub.subscription'));

        foreach ($subscription->pull() as $message) {
            $data = json_decode($message->data(), true);

            // Process the data and send email
            $this->sendEmailNotification($data);

            $subscription->acknowledge($message);
        }
    }

    protected function sendEmailNotification($data)
    {
        // Hardcoded email address
        $userEmail = 'samserajas@gmail.com';

        // Send email with the notification data
        Mail::to($userEmail)->send(new CalendarEventNotification($data));
    }
}

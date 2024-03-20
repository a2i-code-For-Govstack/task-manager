<?php

namespace App\Traits;

use App\Jobs\SendMailNotificationJob;
use App\Jobs\SendNotificationReminderNotificationJob;

trait FireNotification
{
    public function sendMailNotification($mail_notification_data, $options = [])
    {
        if (\Arr::has($options, 'dispatch') && $options['dispatch'] == 1) {
//            SendMailNotificationJob::dispatch($mail_notification_data);
        }
        if (\Arr::has($options, 'delay')) {
//            SendMailNotificationJob::dispatch($mail_notification_data)->delay($options['delay']);
        }
    }

    public function sendPushNotification($notification_data, $options = [])
    {
        if (\Arr::has($options, 'dispatch') && $options['dispatch'] == 1) {
            SendNotificationReminderNotificationJob::dispatch($notification_data);
        }
        if (\Arr::has($options, 'delay')) {
            SendNotificationReminderNotificationJob::dispatch($notification_data)->delay($options['delay']);
        }
    }

}

<?php

namespace App\Http\Controllers;

class PusherController extends Controller
{
    public function notificationAuthorization()
    {
        $authorization_url = config('fire_notification_constants.push_notifier_url') . '/pusher/beams-auth';
        return $this->initHttp()->post($authorization_url, ['employee_record_id' => $this->getOfficerId()])->json();
    }
}

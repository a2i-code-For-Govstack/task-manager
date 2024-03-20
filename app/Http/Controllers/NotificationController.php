<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NotificationController extends Controller
{
    public function getNotifications(Request $request)
    {
        $subscriber = 'task_manager_rid_' . $this->getUserDetails()['employee_record_id'];
        $page = $request->page ?: 1;
        $per_page = $request->per_page ?: 100;


        $notifications = Http::withoutVerifying()->withoutRedirecting()->get(config('ntfy.ntfy_url') . '/v1/subscriber/notifications?publisher_id=pms&subscriber=' . $subscriber . '&page=' . $page . '&per_page=' . $per_page)->json();

        if (isset($notifications['status']) && $notifications['status'] == 'success') {
            $notifications = $notifications['data'];
        } else {
            $notifications = [];
        }
        return view('partials.notifications', compact('notifications'));
    }
}

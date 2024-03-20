<?php

namespace App\Http\Controllers;

use App\Models\UserNotificationSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        return view('modules.settings.index');
    }

    public function loadNotifications(Request $request)
    {
        $user_notification = UserNotificationSetting::where('officer_id', $this->getOfficerId())->first();
        return view('modules.settings.notifications.notifications', compact('user_notification'));
    }

    public function changeUserNotificationSetting(Request $request)
    {
        \Validator::make($request->all(), ['type' => 'required|string', 'officer_id' => 'nullable|integer']);
        try {
            $officer_id = $request->officer_id ?: $this->getOfficerId();
            $type = $request->type;
            $status = $request->status;
            $types = ['web_pusher' => 'web_pusher', 'email' => 'email', 'sms' => 'sms', 'mobile_pusher' => 'mobile_pusher'];
            if (!\Arr::has($types, $type)) {
                throw new \Exception('type not found');
            }

            UserNotificationSetting::updateOrCreate(
                ['officer_id' => $officer_id],
                [$type => $status]
            );

            return response()->json(responseFormat('success', 'Successfully Updated Preferences'));
        } catch (\Exception $exception) {
            return response()->json(responseFormat('error', $exception->getMessage()));
        }

    }
}

<?php
/*
 * Copyright (c) Mahmud S.
 * Github: https://github.com/theeMahmud
 * Gitlab: https://gitlab.com/theeMahmud
 * Skype: mahmud.raju777
 * LinkedIn: https://linkedin.com/in/mahmud-s-raju
 */

namespace App\Services;

use App\Models\Task;
use App\Models\TaskUser;
use App\Traits\FireNotification;
use Exception;
use Log;

class NotificationServices
{
    use FireNotification;

    /**
     * @param $task_id
     * @return array
     */
    public function send_task_welcome_notification($task_id): array
    {
        try {
            $task = Task::find($task_id);
            if (!$task)
                throw new \Exception('Error Code: NSATCNT - Task Not Found.');

            $task_users = TaskUser::where('task_id', $task_id)->with('user_notification_setting')->get();
            if (!$task_users)
                throw new \Exception('Error Code: NSATCNU - Task Users Not Found.');

            $task_organizer = TaskUser::where('task_id', $task_id)->where('user_type', 'organizer')->first();

            foreach ($task_users as $task_user) {
                if ($task_user->user_notification_setting) {
                    if ($task_user->user_notification_setting->email) {
                        $this->set_mail_notification($task_user, $task, $task_organizer);
                    }
                    if ($task_user->user_notification_setting->web_pusher) {
                        $this->set_push_notification($task_user, $task);
                    }
                } else {
                    $this->set_mail_notification($task_user, $task, $task_organizer);
                    $this->set_push_notification($task_user, $task);
                }
            }

            return responseFormat('success', 'Successfully Created!');
        } catch (\Exception $exception) {
            return responseFormat('error', $exception->getMessage());
        }
    }

    public function send_task_assigned_welcome_notification($task_id, $officer_id, $email): array
    {
        try {
            $task = Task::find($task_id);
            if (!$task)
                throw new \Exception('Error Code: NSATCNT - Task Not Found.');

            $this->dispatchPushNotification($officer_id, $task->title_en, 'You have a task assigned!');


            return responseFormat('success', 'Successfully Created!');
        } catch (\Exception $exception) {
            return responseFormat('error', $exception->getMessage());
        }
    }

    /**
     * @param $task_user
     * @param $task
     * @param $task_organizer
     * @return bool
     */
    public function set_mail_notification($task_user, $task, $task_organizer): bool
    {
        try {

            if ($task_user->user_type == 'organizer') {
                $mail_notification_data = [
                    'task_title' => $task->title_en,
                    'task_description' => $task->description,
                    'task_start' => $task->task_start_date_time,
                    'task_end' => $task->task_end_date_time,
                    'sender_email' => $task_user->user_email,
                    'sender_en' => $task_user->user_name_en,
                    'sender_bn' => $task_user->user_name_bn,
                    'receiver_email' => $task_user->user_email,
                    'receiver_en' => $task_user->user_name_en,
                    'receiver_bn' => $task_user->user_name_bn,
                    'task_location' => $task->location,
                    'type' => 'task_creation',
                ];
            } else {
                $mail_notification_data = [
                    'task_title' => $task->title_en,
                    'task_description' => $task->description,
                    'task_start' => $task->task_start_date_time,
                    'task_end' => $task->task_end_date_time,
                    'sender_email' => $task_organizer->user_email,
                    'sender_en' => $task_organizer->user_name_en,
                    'sender_bn' => $task_organizer->user_name_bn,
                    'receiver_email' => $task_user->user_email,
                    'receiver_en' => $task_user->user_name_en,
                    'receiver_bn' => $task_user->user_name_bn,
                    'task_location' => $task->location,
                    'type' => 'task_creation',
                ];
            }
            $this->sendMailNotification($mail_notification_data, ['dispatch' => 1]);
            return true;
        } catch (Exception $exception) {
            return false;
        }
    }

    /**
     * @param $task_user
     * @param $task
     * @return bool
     */
    public function set_push_notification($task_user, $task): bool
    {
        if ($task_user->user_officer_id) {
            $this->dispatchPushNotification($task_user->user_officer_id, $task->title_en, "You Have a Task");
        }
        return true;
    }

    public function dispatchPushNotification($officer_id, $title, $message)
    {
        $notification_data = [
            'recipient' => 'task_manager_rid_' . $officer_id,
            "title" => $title,
            "message" => $message,
        ];
        $this->sendPushNotification($notification_data, ['dispatch' => 1]);
    }

    /**
     * @param $task_id
     */
    public function addTaskReminderNotification($task_id)
    {
    }
}

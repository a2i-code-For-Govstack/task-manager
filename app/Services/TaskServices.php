<?php

namespace App\Services;

use App\Models\CalEvent;
use App\Models\CalEventGuest;
use App\Models\CalEventNotification;
use App\Models\Task;
use App\Models\TaskNotification;
use App\Models\TaskUser;
use App\Traits\ApiHeart;
use App\Traits\FireNotification;
use App\Traits\UserInfoCollector;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskServices
{
    use UserInfoCollector, ApiHeart, FireNotification;

    public function allTasks(Request $request): array
    {
        try {
            $user_task_ids = TaskUser::where('user_officer_id', $request->officer_id)->pluck('task_id');
            $tasks = Task::whereIn('id', $user_task_ids)->where('status', 1)->with('user_task_notifications', function ($query) use ($request) {
                return $query->where('user_officer_id', $request->officer_id)->get();
            })->with('task_user', function ($query) use ($request) {
                return $query->where('user_officer_id', $request->officer_id)->select('id', 'task_id', 'user_type')->get();
            })->with('task_organizer');

            if ($request->page && !$request->all) {
                $tasks = $tasks->orderBy('id', 'DESC')->paginate($request->per_page ?? 10)->toArray();
            } else {
                $tasks = $tasks->orderBy('id', 'DESC')->get()->toArray();
            }

            return responseFormat('success', $tasks);
        } catch (\Exception $exception) {
            return responseFormat('error', $exception->getMessage());
        }
    }

    public function taskDetails($data): array
    {
        try {
            $details = Task::where('id', $data['task_id'])->with('task_users')->first();
            if (!$details) {
                throw new \Exception('Invalid Task Id');
            }
            return responseFormat('success', $details->toArray());
        } catch (\Exception $exception) {
            return responseFormat('error', $exception->getMessage());
        }
    }

    public function getTasks(Request $request): array
    {
        try {
            $user_task_ids = TaskUser::where('user_officer_id', $request->officer_id)
                ->where('user_type', '!=', 'unassigned')
                ->pluck('task_id');

            $parent_tasks = Task::whereIn('id', $user_task_ids)
                ->where('parent_task_id', '!=', 0)
                ->pluck('parent_task_id');

            $user_task_ids = $user_task_ids->merge($parent_tasks);


            $tasks = Task::whereIn('id', $user_task_ids)->where('status', 1)->where('parent_task_id', 0);
            if ($request->task_status == 'completed') {
                $tasks = $tasks->whereHas('task_users', function ($query) use ($request) {
                    $query->where('task_user_status', 'completed')->where('user_type', '!=', 'unassigned')->where('user_officer_id', $request->officer_id);
                });
            }
            if ($request->task_status == 'todo') {
                $tasks = $tasks->where('task_status', 'todo')->where('task_end_date_time', '>', Carbon::now());
            }
            if ($request->task_status == 'pending') {
                $tasks = $tasks->where('task_status', '!=', 'completed')->where('task_status', '!=', 'cancelled')->where('task_end_date_time', '<', Carbon::now());
            }
            if ($request->task_status == 'cancelled') {
                $tasks = $tasks->where('task_status', 'cancelled');
            }
            if ($request->task_status == 'rejected') {
                $tasks = $tasks->where('task_status', 'rejected');
            }
            $tasks = $tasks->with('user_task_notifications', function ($query) use ($request) {
                return $query->where('user_officer_id', $request->officer_id)->get();
            })->with('task_user', function ($query) use ($request) {
                return $query->where('user_officer_id', $request->officer_id)
                    ->where('user_type', '!=', 'unassigned')
                    ->select('id', 'task_id', 'user_type', 'comments', 'has_event', 'task_user_status')
                    ->get();
            })->with('task_organizer');

            $tasks = $tasks->with(['sub_tasks' => function ($q) use ($request) {
                $q->whereHas('task_users', function ($subQuery) use ($request) {
                    $subQuery->where('user_officer_id', $request->officer_id)
                        ->where('user_type', '!=', 'unassigned');
                })->with(['task_user', 'task_organizer'])
                    ->withCount(['task_users' => function ($subQuery) {
                        $subQuery->where('user_type', '!=', 'unassigned');
                    }]);
            }])->withCount(['task_users' => function ($q) {
                $q->where('user_type', '!=', 'unassigned');
            }]);
            if ($request->page && !$request->all) {
                $tasks = $tasks->orderBy('id', 'DESC')->paginate($request->per_page ?? 10)->toArray();
            } else {
                $tasks = $tasks->orderBy('id', 'DESC')->get()->toArray();
            }
            return responseFormat('success', $tasks);
        } catch (\Exception $exception) {
            return responseFormat('error', $exception->getMessage());
        }
    }

    public function storeTask(Request $request): array
    {
        DB::beginTransaction();

        try {
            if (!$request->task_title_en || $request->task_title_en == '') {
                throw new \Exception('Enter Task Title');
            }
            if ($request->task_start_end_date_time) {
                $task_date_time = explode(' - ', $request->task_start_end_date_time);
                $start_date_time = Carbon::createFromFormat('d/m/Y H:i A', $task_date_time[0]);
                $end_date_time = Carbon::createFromFormat('d/m/Y H:i A', $task_date_time[1]);
            } else if ($request->start_date) {
                $start_date = $request->start_date ? Carbon::parse($request->start_date)->format('Y-m-d') : null;
                $end_date = $request->end_date ? Carbon::parse($request->end_date)->format('Y-m-d') : $start_date;
                if ($start_date) {
                    $start_time = $request->start_time ?: Carbon::createFromDate($start_date)->startOfDay()->format('H:i A');
                    $end_time = $request->end_time ?: Carbon::createFromDate($end_date)->endOfDay()->format('H:i A');

                    $start_date_time = \DateTime::createFromFormat('Y-m-d H:i A', "$start_date $start_time", new \DateTimeZone('Asia/Dhaka'));
                    $end_date_time = \DateTime::createFromFormat('Y-m-d H:i A', "$end_date $end_time", new \DateTimeZone('Asia/Dhaka'));
                }
            } else {
                throw new \Exception('Please Specify Task Date Range!');
            }

            if (($start_date_time && $end_date_time) && $request->parent_task_id) {
                $parent_date_time = Task::select('task_start_date_time', 'task_end_date_time')->find($request->parent_task_id);

                if ($start_date_time->lessThan($parent_date_time->task_start_date_time) ||
                    $end_date_time->greaterThan($parent_date_time->task_end_date_time)) {
                    throw new \Exception('Sub task date range should in be task date range');
                }
            }


            $task_creator = json_decode($request->task_organizer, true);

            if (!$task_creator) {
                throw new \Exception('Task Organizer Not Found!');
            }

            $task = [
                'title_en' => $request->task_title_en ?: $request->task_title_bn,
                'title_bn' => $request->task_title_bn ?: $request->task_title_en,
                'description' => $request->task_description,
                'location' => $request->location,
                'task_start_date' => isset($start_date_time) ? $start_date_time->format('Y-m-d') : null,
                'task_end_date' => isset($end_date_time) ? $end_date_time->format('Y-m-d') : null,
                'task_start_date_time' => isset($start_date_time) ? $start_date_time->format('Y-m-d H:i:s') : null,
                'task_end_date_time' => isset($end_date_time) ? $end_date_time->format('Y-m-d H:i:s') : null,
                'system_type' => $request->system_type,
                'task_status' => $request->task_status ?: 'todo',
                'meta_data' => $request->meta_data,
                'parent_task_id' => $request->parent_task_id ?: 0,
                'status' => 1,
            ];

            $created_task = Task::create($task);

            if (!$created_task) {
                throw new \Exception('Task Not Created!');
            }

            $task_organizer = [
                'task_id' => $created_task->id,
                'user_email' => $task_creator['user_email'],
                'user_name_en' => $task_creator['user_name_en'],
                'user_name_bn' => $task_creator['user_name_bn'],
                'username' => \Arr::has($task_creator, 'username') ? $task_creator['username'] : null,
                'user_phone' => \Arr::has($task_creator, 'user_phone') ? $task_creator['user_phone'] : null,
                'user_officer_id' => \Arr::has($task_creator, 'user_officer_id') ? $task_creator['user_officer_id'] : null,
                'user_designation_id' => \Arr::has($task_creator, 'user_designation_id') ? $task_creator['user_designation_id'] : null,
                'user_office_id' => \Arr::has($task_creator, 'user_office_id') ? $task_creator['user_office_id'] : null,
                'user_office_name_en' => \Arr::has($task_creator, 'user_office_name_en') ? $task_creator['user_office_name_en'] : null,
                'user_office_name_bn' => \Arr::has($task_creator, 'user_office_name_bn') ? $task_creator['user_office_name_bn'] : null,
                'user_unit_id' => \Arr::has($task_creator, 'user_unit_id') ? $task_creator['user_unit_id'] : null,
                'user_office_unit_name_en' => \Arr::has($task_creator, 'user_office_unit_name_en') ? $task_creator['user_office_unit_name_en'] : null,
                'user_office_unit_name_bn' => \Arr::has($task_creator, 'user_office_unit_name_bn') ? $task_creator['user_office_unit_name_bn'] : null,
                'user_designation_name_en' => \Arr::has($task_creator, 'user_designation_name_en') ? $task_creator['user_designation_name_en'] : null,
                'user_designation_name_bn' => \Arr::has($task_creator, 'user_designation_name_bn') ? $task_creator['user_designation_name_bn'] : null,
                'user_type' => \Arr::has($task_creator, 'user_type') ? $task_creator['user_type'] : 'organizer',
                'task_user_status' => \Arr::has($task_creator, 'task_user_status') ? $task_creator['task_user_status'] : 'pending',
                'has_event' => ($request->has('task_to_event') && $request->task_to_event == 1) ? 1 : 0,
                'has_assignees' => $request->task_assignee ? 1 : 0,
            ];

            $task_organizer_create = TaskUser::create($task_organizer);

            if ($request->task_assignee) {
                $assignee_data = [
                    'task_id' => $created_task->id,
                    'organizer' => $task_organizer,
                    'task_assignee' => $request->task_assignee,
                ];

                $assignee_create = $this->assignTask($assignee_data);

                if (isSuccess($assignee_create, 'status', 'error')) {
                    throw new \Exception('Assigning Error - ' . json_encode($assignee_create));
                }
            }

            $task_notifications = $request->has('notifications') ? json_decode($request->notifications, true) : [];

            if (!empty($task_notifications)) {

                $notification_data = [
                    'task' => $created_task->toArray(),
                    'notifications' => $task_notifications,
                    'task_user' => $task_organizer_create->toArray(),
                ];
                $update_task_notification = $this->task_notification($notification_data);
                if (isSuccess($update_task_notification, 'status', 'error')) {
                    throw new \Exception(json_encode($update_task_notification));
                }
            }


            $creation_notification = (new NotificationServices())->send_task_welcome_notification($created_task->id);

            if (isSuccess($creation_notification, 'status', 'error')) {
                throw new \Exception('Task Creation Notification Error - ' . json_encode($creation_notification));
            }

            if ($request->has('task_to_event') && $request->task_to_event == 1) {
                $task_to_event_data['task_user'] = $task_organizer;
                $task_to_event_data['task'] = $created_task->toArray();
                $task_to_event_data['notifications'] = $request->notifications ? json_decode($request->notifications, true) : [];

                $event = $this->taskToEvent($task_to_event_data);
                if (isSuccess($event, 'status', 'error')) {
                    throw new \Exception(json_encode($event));
                }
            }
            DB::commit();
            return ['status' => 'success', 'data' => 'Successfully Created'];
        } catch (\Exception $exception) {
            DB::rollBack();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function assignTask($data): array
    {
        DB::beginTransaction();
        try {
            $task_assignee = $data['task_assignee'] ? (is_array($data['task_assignee']) ? $data['task_assignee'] : json_decode($data['task_assignee'], true)) : [];
            $task_assignees = [];
            $existing_assigned_users = TaskUser::where('user_type', '!=', 'organizer')->where('task_id', $data['task_id'])->pluck('user_type', 'user_officer_id');
            $existing_organizer = TaskUser::where('user_type', 'organizer')->where('task_id', $data['task_id'])->first();
            $task_id = $data['task_id'];
            $assigner_officer_id = $data['organizer']['user_officer_id'];
            if ($existing_organizer->user_officer_id != $data['organizer']['user_officer_id']) {
                $task = Task::find($data['task_id']);
                $existing_assigned_user = TaskUser::where('user_officer_id', $assigner_officer_id)->where('task_id', $task_id)->first();
                $new_task = $task->replicate();
                $new_task->meta_data = null;
                $new_task->push();
                $task_id = $new_task->id;
                $new_organizer = $existing_assigned_user->replicate();
                $new_organizer->task_id = $task_id;
                $new_organizer->has_assignees = 1;
                $new_organizer->user_type = 'organizer';
                $new_organizer->push();
            }


            $existing_assigned_users = $existing_assigned_users && $existing_assigned_users->count() > 0 ? $existing_assigned_users->toArray() : [];

            foreach ($task_assignee as $assignee) {

                if ($existing_organizer->user_officer_id == $assignee['user_officer_id']) {
                    continue;
                }

                if (!isset($existing_assigned_users[$assignee['user_officer_id']]) || (isset($existing_assigned_users[$assignee['user_officer_id']]) && $existing_assigned_users[$assignee['user_officer_id']] == 'unassigned')) {
                    $t_assignee = [
                        'task_id' => $task_id,
                        'user_email' => $assignee['user_email'],
                        'user_name_en' => $assignee['user_name_en'],
                        'user_name_bn' => $assignee['user_name_bn'],
                        'username' => \Arr::has($assignee, 'username') ? $assignee['username'] : null,
                        'user_phone' => \Arr::has($assignee, 'user_phone') ? $assignee['user_phone'] : null,
                        'user_officer_id' => \Arr::has($assignee, 'user_officer_id') ? $assignee['user_officer_id'] : null,
                        'user_designation_id' => \Arr::has($assignee, 'user_designation_id') ? $assignee['user_designation_id'] : null,
                        'user_office_id' => \Arr::has($assignee, 'user_office_id') ? $assignee['user_office_id'] : null,
                        'user_office_name_en' => \Arr::has($assignee, 'user_office_name_en') ? $assignee['user_office_name_en'] : null,
                        'user_office_name_bn' => \Arr::has($assignee, 'user_office_name_bn') ? $assignee['user_office_name_bn'] : null,
                        'user_unit_id' => \Arr::has($assignee, 'user_unit_id') ? $assignee['user_unit_id'] : null,
                        'user_office_unit_name_en' => \Arr::has($assignee, 'user_office_unit_name_en') ? $assignee['user_office_unit_name_en'] : null,
                        'user_office_unit_name_bn' => \Arr::has($assignee, 'user_office_unit_name_bn') ? $assignee['user_office_unit_name_bn'] : null,
                        'user_designation_name_en' => \Arr::has($assignee, 'user_designation_name_en') ? $assignee['user_designation_name_en'] : null,
                        'user_designation_name_bn' => \Arr::has($assignee, 'user_designation_name_bn') ? $assignee['user_designation_name_bn'] : null,
                        'assigner_officer_id' => $assigner_officer_id,
                        'user_type' => \Arr::has($assignee, 'user_type') ? $assignee['user_type'] : 'assigned',
                        'task_user_status' => \Arr::has($assignee, 'task_user_status') ? $assignee['task_user_status'] : 'pending',
                        'created_at' => now()->format('Y-m-d H:i:s'),
                        'updated_at' => now()->format('Y-m-d H:i:s'),
                    ];
                    $task_assignees[] = $t_assignee;
                    TaskUser::create($t_assignee);
                    if (isset($assignee['user_officer_id'])) {
                        (new NotificationServices())->send_task_assigned_welcome_notification($task_id, $assignee['user_officer_id'], $assignee['user_email']);
                    }
                }
            }

            TaskUser::where('task_id', $data['task_id'])->where('user_officer_id', $data['organizer']['user_officer_id'])->update(['has_assignees' => 1]);
            DB::commit();
            return responseFormat('success', 'Successfully Assigned User');
        } catch (\Exception $exception) {
            DB::rollBack();
            return responseFormat('error', 'Error Code - ATS ' . $exception->getMessage());
        }
    }

    public function unAssignUser($data): array
    {
        try {
            TaskUser::find($data['task_user_id'])->update(['user_type' => 'unassigned']);
            return responseFormat('success', 'Successfully Un Assigned User');
        } catch (\Exception $exception) {
            return responseFormat('error', $exception->getMessage());
        }
    }

    public function taskToEvent($task_to_event_data): array
    {
        DB::beginTransaction();
        try {
            $start_date_time = Carbon::createFromFormat('Y-m-d H:i:s', $task_to_event_data['task']['task_start_date_time']);
            $end_date_time = Carbon::createFromFormat('Y-m-d H:i:s', $task_to_event_data['task']['task_end_date_time']);
            $event_data = [
                'event_title_en' => $task_to_event_data['task']['title_en'],
                'event_title_bn' => $task_to_event_data['task']['title_bn'],
                'event_description' => \Arr::has($task_to_event_data['task'], 'task_description') ? $task_to_event_data['task']['task_description'] : null,
                'event_start_date_time' => $start_date_time->format('Y-m-d H:i:s'),
                'event_end_date_time' => $end_date_time->format('Y-m-d H:i:s'),
                'event_start_date' => $start_date_time->format('Y-m-d'),
                'event_end_date' => $end_date_time->format('Y-m-d'),
                'event_start_time' => $start_date_time->format('H:i:s'),
                'event_end_time' => $end_date_time->format('H:i:s'),
                'event_location' => \Arr::has($task_to_event_data['task'], 'location') ? $task_to_event_data['task']['location'] : null,
                'task_id' => $task_to_event_data['task']['id'],
                'event_type' => 'task',
                'event_visibility' => \Arr::has($task_to_event_data['task'], 'visibility') ? $task_to_event_data['task']['visibility'] : 'private',
                'status' => 'active',
            ];
            $created_event = CalEvent::create($event_data);

            $event_guests = [
                'event_id' => $created_event->id,
                'user_email' => $task_to_event_data['task_user']['user_email'],
                'user_name_en' => $task_to_event_data['task_user']['user_name_en'],
                'user_name_bn' => $task_to_event_data['task_user']['user_name_bn'],
                'username' => \Arr::has($task_to_event_data['task_user'], 'username') ? $task_to_event_data['task_user']['username'] : null,
                'user_phone' => \Arr::has($task_to_event_data['task_user'], 'user_phone') ? $task_to_event_data['task_user']['user_phone'] : null,
                'user_officer_id' => \Arr::has($task_to_event_data['task_user'], 'user_officer_id') ? $task_to_event_data['task_user']['user_officer_id'] : null,
                'user_designation_id' => \Arr::has($task_to_event_data['task_user'], 'user_designation_id') ? $task_to_event_data['task_user']['user_designation_id'] : null,
                'user_office_id' => \Arr::has($task_to_event_data['task_user'], 'user_office_id') ? $task_to_event_data['task_user']['user_office_id'] : null,
                'user_office_name_en' => \Arr::has($task_to_event_data['task_user'], 'user_office_name_en') ? $task_to_event_data['task_user']['user_office_name_en'] : null,
                'user_office_name_bn' => \Arr::has($task_to_event_data['task_user'], 'user_office_name_bn') ? $task_to_event_data['task_user']['user_office_name_bn'] : null,
                'user_unit_id' => \Arr::has($task_to_event_data['task_user'], 'user_unit_id') ? $task_to_event_data['task_user']['user_unit_id'] : null,
                'user_office_unit_name_en' => \Arr::has($task_to_event_data['task_user'], 'user_office_unit_name_en') ? $task_to_event_data['task_user']['user_office_unit_name_en'] : null,
                'user_office_unit_name_bn' => \Arr::has($task_to_event_data['task_user'], 'user_office_unit_name_bn') ? $task_to_event_data['task_user']['user_office_unit_name_bn'] : null,
                'user_designation_name_en' => \Arr::has($task_to_event_data['task_user'], 'user_designation_name_en') ? $task_to_event_data['task_user']['user_designation_name_en'] : null,
                'user_designation_name_bn' => \Arr::has($task_to_event_data['task_user'], 'user_designation_name_bn') ? $task_to_event_data['task_user']['user_designation_name_bn'] : null,
                'user_type' => \Arr::has($task_to_event_data['task_user'], 'user_type') ? $task_to_event_data['task_user']['user_type'] : null,
                'visibility_type' => 'private',
                'tag_color' => 'fc fc-event-success',
                'acceptance_status' => 'accepted',
            ];
            $event_guest = CalEventGuest::create($event_guests);

            $cal_event_organizer_data = CalEventGuest::where('user_type', 'organizer')->where('event_id', $created_event->id)->first();
            $notifications = $task_to_event_data['notifications'];
            $processed_notifications = [];
            // if ($notifications) {
            //     foreach ($notifications as $notification) {
            //         if ($notification['unit'] == 'minutes') {
            //             $notification_time = Carbon::create($start_date_time->format('Y-m-d H:i:s'))->subMinutes($notification['interval']);
            //         } elseif ($notification['unit'] == 'hours') {
            //             $notification_time = Carbon::create($start_date_time->format('Y-m-d H:i:s'))->subHours($notification['interval']);
            //         } elseif ($notification['unit'] == 'days') {
            //             $notification_time = Carbon::create($start_date_time->format('Y-m-d H:i:s'))->subDays($notification['interval']);
            //         } elseif ($notification['unit'] == 'weeks') {
            //             $notification_time = Carbon::create($start_date_time->format('Y-m-d H:i:s'))->subWeeks($notification['interval']);
            //         } else {
            //             $notification_time = Carbon::create($start_date_time->format('Y-m-d H:i:s'));
            //         }

            //         $processed_notification = [
            //             'event_id' => $created_event->id,
            //             'user_email' => $event_guest['user_email'],
            //             'user_officer_id' => $event_guest['user_officer_id'],
            //             'user_designation_id' => $event_guest['user_designation_id'],
            //             'username' => $event_guest['username'],
            //             'event_notification' => $notification_time->format('Y-m-d H:i:s'),
            //             'notification_medium' => $notification['medium'],
            //             'unit' => $notification['unit'],
            //             'interval' => $notification['interval'],
            //             'is_dispatched' => \Arr::has($notification, 'is_dispatched') ? $notification['is_dispatched'] : 0,
            //         ];
            //         $processed_notifications[] = $processed_notification;

            //         if ($notification['medium'] == 'email') {
            //             if ($event_guest['user_type'] == 'organizer') {
            //                 $mail_notification_data = [
            //                     'event_title' => $task_to_event_data['task']['title_en'],
            //                     'event_description' => $task_to_event_data['task']['description'],
            //                     'event_start' => $start_date_time->format('Y-m-d H:i:s'),
            //                     'event_end' => $end_date_time->format('Y-m-d H:i:s'),
            //                     'sender_email' => $event_guest['user_email'],
            //                     'sender_en' => $event_guest['user_name_en'],
            //                     'sender_bn' => $event_guest['user_name_bn'],
            //                     'receiver_email' => $event_guest['user_email'],
            //                     'receiver_en' => $event_guest['user_name_en'],
            //                     'receiver_bn' => $event_guest['user_name_bn'],
            //                 ];
            //             } else {
            //                 $mail_notification_data = [
            //                     'event_title' => $task_to_event_data['task']['title_en'],
            //                     'event_description' => $task_to_event_data['task']['description'],
            //                     'event_start' => $start_date_time->format('Y-m-d H:i:s'),
            //                     'event_end' => $end_date_time->format('Y-m-d H:i:s'),
            //                     'sender_email' => $cal_event_organizer_data->user_email,
            //                     'sender_en' => $cal_event_organizer_data->user_name_en,
            //                     'sender_bn' => $cal_event_organizer_data->user_name_bn,
            //                     'receiver_email' => $event_guest['user_email'],
            //                     'receiver_en' => $event_guest['user_name_en'],
            //                     'receiver_bn' => $event_guest['user_name_bn'],
            //                 ];
            //             }

            //             $this->sendMailNotification($mail_notification_data, ['dispatch' => 1, 'delay' => $notification_time]);
            //         } elseif ($notification['medium'] == 'notification') {
            //             if (\Arr::has($event_guest, 'user_officer_id')) {
            //                 $notification_data = [
            //                     'users' => ["1_" . $event_guest['user_officer_id']],
            //                     "title" => $task_to_event_data['task']['title_en'],
            //                     "data" => json_encode($task_to_event_data['task']),
            //                     "body" => "You Have A Task Assigned",
            //                     "subtitle" => "You Have A Task Assigned",
            //                 ];
            //                 $this->sendPushNotification($notification_data, ['dispatch' => 1, 'delay' => $notification_time]);
            //             }
            //         }
            //     }
            // }
            // $created_notifications = CalEventNotification::insert($processed_notifications);

            if ($event_guest && $created_event) {
                DB::commit();
                return ['status' => 'success', 'data' => 'Created!'];
            } else {
                throw new \Exception('guests =>' . $event_guest . ' event => ' . json_encode($created_event->toArray()));
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function pendingTasks(Request $request): array
    {
        try {
            $user_task_ids = TaskUser::where('user_officer_id', $request->officer_id)->where(function ($query) {
                return $query->where('user_type', 'assigned')->orWhere(function ($query) {
                    return $query->where('user_type', 'organizer')->where('has_assignees', '!=', 1)->orWhereNull('has_assignees');
                });
            })->distinct('task_id')->pluck('task_id');
            $pending_tasks = Task::whereIn('id', $user_task_ids)->where('status', 1)
//                ->where('task_status', '!=', 'completed')
//                ->where('task_status', '!=', 'cancelled')
                ->whereIn('task_status', ['pending', 'todo'])
                ->with('user_task_notifications', function ($query) use ($request) {
                    return $query->where('user_officer_id', $request->officer_id)->get();
                })->with('task_user', function ($query) use ($request) {
                    return $query->where('user_officer_id', $request->officer_id)
                        ->select('id', 'task_id', 'user_type', 'comments', 'has_event', 'task_user_status')
                        ->where('user_officer_id', $request->officer_id)
                        ->whereIn('task_user_status', ['pending', 'todo'])
                        ->get();
                })->with('task_organizer');

            if ($request->search_params) {
                $search_params = explode('&', $request->search_params);
                $search_condition = [];
                $or_search_condition = [];
                $between_search_condition = [];
                foreach ($search_params as $search_param) {
                    $param = explode('=', $search_param);
                    if (is_array($param) && isset($param[0]) && isset($param[1])) {
                        $field = $param[0];
                        $value = $param[1];
                        if ($field == 'task_date') {
                            $date_time_range = explode('-', $value);
                            $start_date = $date_time_range[0];
                            $end_date = $date_time_range[1];
                            $start_date = Carbon::createFromFormat('d/m/Y', $start_date)->format('Y-m-d');
                            $end_date = Carbon::createFromFormat('d/m/Y', $end_date)->format('Y-m-d');
                            $pending_tasks = $pending_tasks->where('task_start_date', '>=', $start_date)->where('task_end_date', '<=', $end_date);
                        }
                        if ($field == 'task_title') {
                            $pending_tasks = $pending_tasks->where('title_en', 'like', '%' . $value . '%')->orWhere('title_bn', 'like', '%' . $value . '%');
                        }
                    }
                }
            }


            if ($request->page && !$request->all) {
                $pending_tasks = $pending_tasks->orderBy('id', 'DESC')->paginate($request->per_page ?? 10)->toArray();
            } else {
                $pending_tasks = $pending_tasks->orderBy('id', 'DESC')->get()->toArray();
            }
            return responseFormat('success', $pending_tasks);
        } catch (\Exception $exception) {
            return responseFormat('error', $exception->getMessage());
        }
    }

    public function dailyTasks(Request $request): array
    {
        try {
            $user_task_ids = TaskUser::where('user_officer_id', $request->officer_id)->pluck('task_id');
            $daily_tasks = Task::whereIn('id', $user_task_ids)->where('status', 1)->whereDate('task_start_date_time', Carbon::today());
            if ($request->page && !$request->all) {
                $daily_tasks = $daily_tasks->orderBy('id', 'DESC')->paginate($request->per_page ?? 10)->toArray();
            } else {
                $daily_tasks = $daily_tasks->orderBy('id', 'DESC')->get()->toArray();
            }
            return responseFormat('success', $daily_tasks);
        } catch (\Exception $exception) {
            return responseFormat('error', $exception->getMessage());
        }
    }

    public function updateTask(Request $request): array
    {
        DB::beginTransaction();
        try {
            $task_user = json_decode($request->task_user, true);
            if ($task_user['user_type'] == 'organizer') {
                $start_date = $request->start_date ? Carbon::parse($request->start_date)->format('Y-m-d') : null;
                $end_date = $request->end_date ? Carbon::parse($request->end_date)->format('Y-m-d') : $start_date;
                if ($start_date) {
                    $start_time = $request->start_time ?: Carbon::createFromDate($start_date)->startOfDay()->format('H:i A');
                    $end_time = $request->end_time ?: Carbon::createFromDate($end_date)->endOfDay()->format('H:i A');

                    $start_date_time = \DateTime::createFromFormat('Y-m-d H:i A', "$start_date $start_time", new \DateTimeZone('Asia/Dhaka'));
                    $end_date_time = \DateTime::createFromFormat('Y-m-d H:i A', "$end_date $end_time", new \DateTimeZone('Asia/Dhaka'));
                }

                if ($request->task_start_end_date_time) {
                    $task_date_time = explode(' - ', $request->task_start_end_date_time);
                    $start_date_time = Carbon::createFromFormat('d/m/Y H:i A', $task_date_time[0]);
                    $end_date_time = Carbon::createFromFormat('d/m/Y H:i A', $task_date_time[1]);
                }

                $task = [
                    'title_en' => $request->task_title_en,
                    'title_bn' => $request->task_title_bn,
                    'description' => $request->task_description,
                    'location' => $request->location,
                    'task_start_date' => isset($start_date_time) ? $start_date_time->format('Y-m-d') : null,
                    'task_end_date' => isset($end_date_time) ? $end_date_time->format('Y-m-d') : null,
                    'task_start_date_time' => isset($start_date_time) ? $start_date_time->format('Y-m-d H:i:s') : null,
                    'task_end_date_time' => isset($end_date_time) ? $end_date_time->format('Y-m-d H:i:s') : null,
                    'task_status' => $request->task_status ?: 'todo',
                    'status' => $request->has('status') ? $request->status : 1,
                ];

                $updated_task = Task::where('id', $request->task_id)->update($task);
                $task['id'] = $request->task_id;

                $task_notifications = $request->has('notifications') ? json_decode($request->notifications, true) : [];
                $notification_data = [
                    'task' => $task,
                    'notifications' => $task_notifications,
                    'task_user' => $task_user,
                ];

                $update_task_notification = $this->task_notification($notification_data);

                if (isSuccess($update_task_notification, 'status', 'error')) {
                    throw new \Exception(json_encode($update_task_notification));
                }

                if ($request->has('task_assignee')) {
                    $assignee_data = [
                        'task_id' => $request->task_id,
                        'organizer' => $task_user,
                        'task_assignee' => $request->task_assignee,
                    ];
                    $assigned_task = $this->assignTask($assignee_data);
                    if (isSuccess($assigned_task, 'status', 'error')) {
                        throw new \Exception(json_encode($assigned_task));
                    }
                }

                if ($request->has('task_to_event')) {
                    if ($request->task_to_event == 1) {
                        $task_to_event_data['task_user'] = $task_user;
                        $task_to_event_data['task'] = $task;
                        $task_to_event_data['notifications'] = $request->notifications ? json_decode($request->notifications, true) : [];
                        $event = $this->taskToEvent($task_to_event_data);
                    } else {
                        $task = Task::find($request->task_id)->update(['has_event', 0]);
                        $event = CalEvent::where('task_id', $request->task_id)->first();
                        if ($event) {
                            CalEventGuest::where('event_id', $event->id)->delete();
                            $event->delete();
                        }
                    }
                }
            } else {
                // if ($request->has('task_assignee') && $request->task_assignee && json_decode($request->task_assignee, true)) {
                //     $task_assignees = json_decode($request->task_assignee, true);
                //     foreach ($task_assignees as $task_assignee) {
                //         $task_date_time = explode(' - ', $task_assignee['task_date_time']);
                //         $start_date_time = Carbon::createFromFormat('d/m/Y H:i A', $task_date_time[0]);
                //         $end_date_time = Carbon::createFromFormat('d/m/Y H:i A', $task_date_time[1]);

                //         $task_data = [
                //             'title_en' => $request->task_title_en ?: $request->task_title_bn,
                //             'title_bn' => $request->task_title_bn ?: $request->task_title_en,
                //             'description' => $request->task_description,
                //             'location' => $request->location,
                //             'task_start_date' => isset($start_date_time) ? $start_date_time->format('Y-m-d') : null,
                //             'task_end_date' => isset($end_date_time) ? $end_date_time->format('Y-m-d') : null,
                //             'task_start_date_time' => isset($start_date_time) ? $start_date_time->format('Y-m-d H:i:s') : null,
                //             'task_end_date_time' => isset($end_date_time) ? $end_date_time->format('Y-m-d H:i:s') : null,
                //             'system_type' => $request->system_type ?: 'web',
                //             'task_status' => $request->task_status ?: 'todo',
                //             'meta_data' => $request->meta_data ?: '',
                //             'status' => 1,
                //         ];

                //         $task = Task::create($task_data);
                //         if ($task) {
                //             $task = $task->toArray();
                //         } else {
                //             throw new \Exception('New Task Creation Error.');
                //         }

                //         $task_user_organizer = [
                //             'task_id' => $task['id'],
                //             'user_email' => $task_user['user_email'],
                //             'user_name_en' => $task_user['user_name_en'],
                //             'user_name_bn' => $task_user['user_name_bn'],
                //             'username' => \Arr::has($task_user, 'username') ? $task_user['username'] : null,
                //             'user_phone' => \Arr::has($task_user, 'user_phone') ? $task_user['user_phone'] : null,
                //             'user_officer_id' => \Arr::has($task_user, 'user_officer_id') ? $task_user['user_officer_id'] : null,
                //             'user_designation_id' => \Arr::has($task_user, 'user_designation_id') ? $task_user['user_designation_id'] : null,
                //             'user_office_id' => \Arr::has($task_user, 'user_office_id') ? $task_user['user_office_id'] : null,
                //             'user_office_name_en' => \Arr::has($task_user, 'user_office_name_en') ? $task_user['user_office_name_en'] : null,
                //             'user_office_name_bn' => \Arr::has($task_user, 'user_office_name_bn') ? $task_user['user_office_name_bn'] : null,
                //             'user_unit_id' => \Arr::has($task_user, 'user_unit_id') ? $task_user['user_unit_id'] : null,
                //             'user_office_unit_name_en' => \Arr::has($task_user, 'user_office_unit_name_en') ? $task_user['user_office_unit_name_en'] : null,
                //             'user_office_unit_name_bn' => \Arr::has($task_user, 'user_office_unit_name_bn') ? $task_user['user_office_unit_name_bn'] : null,
                //             'user_designation_name_en' => \Arr::has($task_user, 'user_designation_name_en') ? $task_user['user_designation_name_en'] : null,
                //             'user_designation_name_bn' => \Arr::has($task_user, 'user_designation_name_bn') ? $task_user['user_designation_name_bn'] : null,
                //             'user_type' => 'organizer',
                //             'has_event' => $request->task_to_event ?? 0,
                //             'has_assignee' => 1,
                //         ];

                //         $task_user_create = TaskUser::create($task_user_organizer);
                //         $assignee_data = [
                //             'task_id' => $task['id'],
                //             'organizer' => $task_user,
                //             'task_assignee' => [$task_assignee],
                //         ];
                //         $assigned_task = $this->assignTask($assignee_data);
                //         if (isSuccess($assigned_task, 'status', 'error')) {
                //             throw new \Exception(json_encode($assigned_task));
                //         }
                //     }
                // }

                $task = Task::find($request->task_id);
                $task_notifications = $request->has('notifications') ? json_decode($request->notifications, true) : [];

                if (!empty($task_notifications)) {

                    $notification_data = [
                        'task' => $task,
                        'notifications' => $task_notifications,
                        'task_user' => $task_user,
                    ];
                    $update_task_notification = $this->task_notification($notification_data);
                    if (isSuccess($update_task_notification, 'status', 'error')) {
                        throw new \Exception(json_encode($update_task_notification));
                    }
                }

                if ($request->has('task_to_event')) {
                    if ($request->task_to_event == 1) {
                        $task_user_update = TaskUser::where('task_id', $request->task_id)->where('user_officer_id', $task_user['user_officer_id'])->update(['has_event' => 1]);
                        $task_to_event_data['task_user'] = $task_user;
                        $task_to_event_data['task'] = $task;
                        $task_to_event_data['notifications'] = $request->notifications ? json_decode($request->notifications, true) : [];
                        $event = $this->taskToEvent($task_to_event_data);
                        if (isSuccess($event, 'status', 'error')) {
                            throw new \Exception(json_encode($event));
                        }
                    } else {
                        TaskUser::where('task_id', $task['id'])->where('user_officer_id', $task_user['user_officer_id'])->update(['has_event' => 0]);
                        $event = CalEvent::where('task_id', $task['id'])->first();
                        if ($event) {
                            CalEventGuest::where('event_id', $event->id)->delete();
                            $event->delete();
                        }
                    }
                }
            }

            DB::commit();
            return ['status' => 'success', 'data' => 'Successfully Updated'];
        } catch (\Exception $exception) {
            DB::rollBack();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function task_notification($data): array
    {
        try {
            //TODO:remove queued tasks
            TaskNotification::where('user_officer_id', $data['task_user']['user_officer_id'])->where('task_id', $data['task']['id'])->where('is_dispatched', 0)->delete();

            $start_date_time = Carbon::createFromFormat('Y-m-d H:i:s', $data['task']['task_start_date_time']);
            $end_date_time = Carbon::createFromFormat('Y-m-d H:i:s', $data['task']['task_end_date_time']);
            $notifications = is_array($data['notifications']) ? $data['notifications'] : json_decode($data['notifications'], true);
            $task_user = $data['task_user'];
            $processed_notifications = [];
            $task_organizer_data = TaskUser::where('task_id', $data['task']['id'])
                ->where('user_type', 'organizer')
                ->first();
            if ($notifications) {
                foreach ($notifications as $notification) {
                    if ($notification['unit'] == 'minutes') {
                        $notification_time = Carbon::create($start_date_time->format('Y-m-d H:i:s'))->subMinutes($notification['interval']);
                    } elseif ($notification['unit'] == 'hours') {
                        $notification_time = Carbon::create($start_date_time->format('Y-m-d H:i:s'))->subHours($notification['interval']);
                    } elseif ($notification['unit'] == 'days') {
                        $notification_time = Carbon::create($start_date_time->format('Y-m-d H:i:s'))->subDays($notification['interval']);
                    } elseif ($notification['unit'] == 'weeks') {
                        $notification_time = Carbon::create($start_date_time->format('Y-m-d H:i:s'))->subWeeks($notification['interval']);
                    } else {
                        $notification_time = Carbon::create($start_date_time->format('Y-m-d H:i:s'));
                    }

                    $processed_notification = [
                        'task_id' => $data['task']['id'],
                        'user_email' => $task_user['user_email'],
                        'user_officer_id' => $task_user['user_officer_id'],
                        'user_designation_id' => $task_user['user_designation_id'],
                        'username' => $task_user['username'],
                        'event_notification' => $notification_time->format('Y-m-d H:i:s'),
                        'unit' => $notification['unit'],
                        'interval' => $notification['interval'],
                        'notification_medium' => $notification['medium'],
                        'is_dispatched' => \Arr::has($notification, 'is_dispatched') ? $notification['is_dispatched'] : 0,
                    ];
                    $processed_notifications[] = $processed_notification;

                    if ($notification['medium'] == 'email') {
                        if ($task_user['user_type'] == 'organizer') {
                            $mail_notification_data = [
                                'event_title' => $data['task']['title_en'],
                                'event_description' => $data['task']['description'],
                                'event_start' => $start_date_time->format('Y-m-d H:i:s'),
                                'event_end' => $end_date_time->format('Y-m-d H:i:s'),
                                'sender_email' => $task_user['user_email'],
                                'sender_en' => $task_user['user_name_en'],
                                'sender_bn' => $task_user['user_name_bn'],
                                'receiver_email' => $task_user['user_email'],
                                'receiver_en' => $task_user['user_name_en'],
                                'receiver_bn' => $task_user['user_name_bn'],
                            ];
                        } else {
                            $mail_notification_data = [
                                'event_title' => $data['task']['title_en'],
                                'event_description' => $data['task']['description'],
                                'event_start' => $start_date_time->format('Y-m-d H:i:s'),
                                'event_end' => $end_date_time->format('Y-m-d H:i:s'),
                                'sender_email' => $task_organizer_data->user_email,
                                'sender_en' => $task_organizer_data->user_name_en,
                                'sender_bn' => $task_organizer_data->user_name_bn,
                                'receiver_email' => $task_user['user_email'],
                                'receiver_en' => $task_user['user_name_en'],
                                'receiver_bn' => $task_user['user_name_bn'],
                            ];
                        }

                        $this->sendMailNotification($mail_notification_data, ['dispatch' => 1, 'delay' => $notification_time]);
                    } elseif ($notification['medium'] == 'notification') {
                        if (\Arr::has($task_user, 'user_officer_id') && $task_user['user_type'] == 'assigned') {
                            $notification_data = [
                                "title" => $data['task']['title_en'],
                                "message" => "You Have A Task Assigned",
                                'recipient' => "task_manager_rid_" . $task_user['user_officer_id'],
                            ];
                            $this->sendPushNotification($notification_data, ['dispatch' => 1, 'delay' => $notification_time]);
                        }
                    }
                }
            }
            $created_notifications = TaskNotification::insert($processed_notifications);
            return responseFormat('success', 'Added Notification');
        } catch (\Exception $exception) {
            return responseFormat('error', $exception->getMessage());
        }
    }

    public function updateTaskStatus(Request $request): array
    {
        DB::beginTransaction();
        try {
            if (!$request->task_status) {
                throw new \Exception('Specify Status Value');
            }
            if (!$request->officer_id) {
                throw new \Exception('Provide Officer Id');
            }

            if (!$request->task_id) {
                throw new \Exception('Specify Task ID');
            }
            $task_id = $request->task_id;
            $task = Task::find($task_id);
            if ($task) {
                $task_user = TaskUser::where('task_id', $request->task_id)->where('user_officer_id', $request->officer_id)->first();
                if ($task_user->user_type == 'organizer') {
                    TaskUser::where('task_id', $request->task_id)->update(['task_user_status' => $request->task_status]);
                    $task->update(['task_status' => $request->task_status]);
                } else {
                    $task_user->update(['task_user_status' => $request->task_status]);
                }
                DB::commit();
                return responseFormat('success', 'Task Status Updated Successfully.');
            } else {
                throw new \Exception('Task Not Found');
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return responseFormat('error', $exception->getMessage());
        }
    }

    public function getTaskUsers($data): array
    {
        try {
            $task_users = TaskUser::where('task_id', $data['task_id']);
            if (\Arr::has($data, 'user_type')) {
                $task_users = $task_users->where('user_type', $data['user_type']);
            } else {
                $task_users = $task_users->where('user_type', '!=', 'unassigned');
            }
            $task_users = $task_users->get();
            return responseFormat('success', $task_users);
        } catch (\Exception $exception) {
            return responseFormat('error', $exception->getMessage());
        }
    }

    public function deleteTask($task_id): array
    {
        try {
            $task = Task::where('id', $task_id)->with('task_users')->first();
            if ($task) {
                if ($task->task_users->count() > 1) {
                    $task = $task->update(['task_status' => 'cancelled']);
                    TaskUser::where('task_id', $task_id)->where('user_type', 'organizer')->update(['task_user_status' => 'cancelled']);
                    return responseFormat('success', 'Successfully Cancelled Task!');
                } else {
                    TaskUser::where('task_id', $task_id)->delete();
                    TaskNotification::where('task_id', $task_id)->delete();
                    $task_events = CalEvent::where('task_id', $task_id)->pluck('id');
                    if ($task_events) {
                        CalEventGuest::whereIn('event_id', $task_events)->delete();
                        CalEventNotification::whereIn('event_id', $task_events)->delete();
                        CalEvent::where('task_id', $task_id)->delete();
                    }
                    $task->delete();
                    return responseFormat('success', 'Successfully Deleted Task!');
                }
            } else {
                throw new \Exception('Task Not Found!');
            }
        } catch (\Exception $exception) {
            return responseFormat('error', $exception->getMessage());
        }
    }

    public function assignTaskMultiple($data)
    {
        DB::beginTransaction();
        try {
            $task_ids = is_array($data['tasks']) ? $data['tasks'] : json_decode($data['tasks'], true);
            $assigner_officer_id = \Arr::has($data, 'assigner_officer_id') ? $data['assigner_officer_id'] : $this->getOfficerId();
            // $task_ids = TaskUser::where('user_officer_id', $assigner_officer_id)->whereIn('task_id', $task_ids)->where('user_type', 'organizer')->pluck('task_id');
            foreach ($task_ids as $task_id) {
                $task_data = [
                    'task_id' => $task_id,
                    'task_assignee' => $data['task_assignees'],
                    'organizer' => [
                        'user_officer_id' => $assigner_officer_id,
                    ],
                ];

                $assigned = $this->assignTask($task_data);
                if (isSuccess($assigned, 'status', 'error')) {
                    throw new \Exception($assigned['message']);
                }
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return responseFormat('error', 'Error Code - ATM ' . $exception->getMessage());
        }
    }

    public function getTaskAssignees(Request $request)
    {
        $task_assignees = TaskUser::where('assigner_officer_id', $request->officer_id)->whereHas('task', function ($query) {
//            return $query->where('task_status', '!=', 'completed')->where('task_status', '!=', 'cancelled');
            return $query->whereIn('task_status', ['pending', 'todo']);
        })->with('task', function ($query) {
            return $query->whereIn('task_status', ['pending', 'todo']);
        });
        return $task_assignees->paginate(10);
    }

    public function assignedTaskToOthers($officer_id)
    {
        $task_assign = TaskUser::where('assigner_officer_id', $officer_id)->where('user_type', '!=', 'organizer')->whereHas('task', function ($query) {
            return $query->where('task_status', '!=', 'completed')->where('task_status', '!=', 'cancelled');
        })->with('task', function ($query) {
            return $query->where('task_status', '!=', 'completed')->where('task_status', '!=', 'cancelled');
        })->orderBy('id', 'DESC')->paginate(10);
        $task_assign = $task_assign ? $task_assign->toArray() : [];

        return responseFormat('success', $task_assign);
    }

    public function getTasksCountTypeWise($officer_id)
    {
        $user_task_ids = TaskUser::where('user_officer_id', $officer_id)->where('user_type', '!=', 'unassigned')->select('task_id')->pluck('task_id');

        $tasks = Task::query();
        $tasks = $tasks->whereIn('id', $user_task_ids)->where('status', 1)->where('parent_task_id', 0);

        $completed = (clone $tasks)->whereHas('task_users', function ($query) use ($officer_id) {
            $query->where('task_user_status', 'completed')->where('user_officer_id', $officer_id);
        })->count();

        $todo = (clone $tasks)->where('task_status', 'todo')->where('task_end_date_time', '>', Carbon::now())->count();

        $pending = (clone $tasks)->where('task_status', '!=', 'completed')->where('task_status', '!=', 'cancelled')->where('task_end_date_time', '<', Carbon::now())->count();

        $cancelled = (clone $tasks)->where('task_status', 'cancelled')->count();

        $rejected = (clone $tasks)->where('task_status', 'rejected')->count();

        return [
            'completed' => $completed,
            'todo' => $todo,
            'pending' => $pending,
            'cancelled' => $cancelled,
            'rejected' => $rejected,
        ];
    }
}

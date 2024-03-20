<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskNotification;
use App\Models\TaskUser;
use App\Services\CalEventGuestServices;
use App\Services\TaskServices;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(Request $request, TaskServices $taskServices)
    {
        $task_setup = $request->has('task_setup') ? $request->task_setup : '';
        return view('tasks.all_tasks', compact('task_setup'));
    }

    public function loadAllTasks(Request $request)
    {
        $task_setup = $request->has('task_setup') ? $request->task_setup : '';
        $task_counts = (new TaskServices())->getTasksCountTypeWise($this->getOfficerId());
//        dd($task_counts);
        return view('tasks.load_all_tasks', compact('task_setup', 'task_counts'));
    }

    public function getTasksList(Request $request, TaskServices $taskServices)
    {
        $request->merge(['officer_id' => $this->getOfficerId()]);
        $tasks = $taskServices->getTasks($request);
        if (isSuccess($tasks)) {
            $tasks = $tasks['data'];
            return view('tasks.tasks_table', compact('tasks'));
        } else {
            return response()->json(responseFormat('error', $tasks['message']));
        }
    }


    public function create(Request $request)
    {
        $organizer = [
            'user_email' => $this->getPersonalEmail(),
            'user_name_en' => $this->getEmployeeInfo()['name_eng'],
            'user_name_bn' => $this->getEmployeeInfo()['name_bng'],
            'username' => $this->getUsername(),
            'user_phone' => $this->getEmployeeInfo()['personal_mobile'],
            'user_officer_id' => $this->getEmployeeInfo()['id'],
            'user_designation_id' => $this->current_designation_id(),
            'user_office_id' => $this->current_office_id(),
            'user_office_name_en' => $this->current_office()['office_name_en'],
            'user_office_name_bn' => $this->current_office()['office_name_bn'],
            'user_unit_id' => $this->current_office_unit_id(),
            'user_office_unit_name_en' => $this->current_office()['unit_name_en'],
            'user_office_unit_name_bn' => $this->current_office()['unit_name_bn'],
            'user_designation_name_en' => $this->current_office()['designation_en'],
            'user_designation_name_bn' => $this->current_office()['designation'],
            'user_type' => 'organizer',
        ];

        $parent_id = $request->parent_id;
        $parent_task = Task::where('id', $parent_id)->select('id', 'title_en', 'task_start_date_time', 'task_end_date_time')->first();

        return view('tasks.create_task', compact('organizer', 'parent_id', 'parent_task'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function store(Request $request, TaskServices $taskServices)
    {
        $store_task = $taskServices->storeTask($request);
        return response()->json($store_task);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Task $task
     * @return Response
     */
    public function show(Request $request)
    {
        $task_info = Task::whereHas('task_users', function ($query) {
            return $query->where('user_type', '!=', 'unassigned');
        })->with('task_users', function ($query) {
            return $query->where('user_type', '!=', 'unassigned');
        })->find($request->task_id);

        return view('tasks.show_task', compact('task_info'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @return Application|Factory|View|JsonResponse
     */
    public function edit(Request $request)
    {
        $parent_id = $request->parent_id;
        $task = Task::find($request->task_id);
        if ($task) {
            $task = $task->toArray();
            $task_user = TaskUser::where('task_id', $request->task_id)->where('user_officer_id', $this->getOfficerId())->first();
            $task_user = $task_user ? $task_user->toArray() : [];
            $task_notifications = TaskNotification::where('user_officer_id', $this->getOfficerId())->where('task_id', $request->task_id)->get();
            if ($task_user['user_type'] == 'organizer') {
                $task_all_users = TaskUser::where('task_id', $request->task_id)->get();
                $task_all_users = $task_all_users ? $task_all_users->toArray() : [];
            } else {
                $task_all_users = [];
            }
            $task_notifications = $task_notifications ? $task_notifications->toArray() : [];
            $user_task_event = $task_user['has_event'] == 1 ? 1 : 0;
            if ($request->has('edit_type') && $request->edit_type == 'assign') {
                return view('tasks.assign_new_task_edit', compact('task', 'task_user', 'task_notifications', 'task_all_users', 'user_task_event', 'parent_id'));
            } else {
                return view('tasks.edit_task', compact('task', 'task_user', 'task_notifications', 'task_all_users', 'user_task_event', 'parent_id'));
            }
        } else {
            return response()->json(responseFormat('error', 'Task Not Found.'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Task $task
     * @return JsonResponse
     */
    public function update(Request $request, TaskServices $taskServices): JsonResponse
    {
        $update_task = $taskServices->updateTask($request);
        return response()->json($update_task);
    }

    public function updateStatus(Request $request, TaskServices $taskServices): JsonResponse
    {
        $request->merge(['officer_id' => $this->getOfficerId()]);
        $task_completion = $taskServices->updateTaskStatus($request);
        return response()->json($task_completion);
    }

    public function searchAssignee(Request $request, CalEventGuestServices $calEventGuestServices)
    {
        $data = [
            'office_id' => $request->office_id,
            'unit_id' => $request->unit_id,
            'search_key' => $request->search_key,
        ];

        $search_key = $request->search_key;
        $type = $request->type;


        $searched_users = $calEventGuestServices->searchGuestByEmailOrName($data);

//        dd($searched_users);

        return view('tasks.searched_assignee', compact('searched_users', 'search_key', 'type'));
    }

    public function searchAssignedTaskByUserInfo(Request $request)
    {
        try {
            $search_key = $request->search_key;
            $task_assignees = TaskUser::where('assigner_officer_id', $this->getOfficerId())->where(function ($query) use ($search_key) {
                $query->where('user_name_en', 'like', '%' . $search_key . '%')
                    ->orWhere('user_name_en', 'like', '%' . $search_key . '%')
                    ->orWhere('user_name_bn', 'like', '%' . $search_key . '%')
                    ->orWhere('user_designation_name_en', 'like', '%' . $search_key . '%')
                    ->orWhere('user_designation_name_bn', 'like', '%' . $search_key . '%')
                    ->orWhere('user_office_unit_name_en', 'like', '%' . $search_key . '%')
                    ->orWhere('user_office_unit_name_bn', 'like', '%' . $search_key . '%');
            })->whereHas('task', function ($query) {
                return $query->where('task_status', '!=', 'completed')->where('task_status', '!=', 'cancelled');
            })->with('task', function ($query) {
                return $query->where('task_status', '!=', 'completed')->where('task_status', '!=', 'cancelled');
            })->orderBy('id', 'DESC')->paginate(10);
            $route = 'tasks.search.assigned.user-info';
            $task_assignees = $task_assignees ? $task_assignees->toArray() : [];
            return view('tasks.search.assigned.user_info', compact('task_assignees', 'search_key', 'route'));
        } catch (\Exception $exception) {
            return response()->json(responseFormat('error', $exception->getMessage()));
        }
    }

    public function searchAssignedTaskByTaskInfo(Request $request)
    {
        try {
            $search_key = $request->search_key;
            $task_assignees = TaskUser::where('assigner_officer_id', $this->getOfficerId())
                ->whereHas('task', function ($query) use ($search_key) {
                    return $query->where('title_en', 'like', '%' . $search_key . '%')->orWhere('title_bn', 'like', '%' . $search_key . '%')
                        ->where('task_status', '!=', 'completed')->where('task_status', '!=', 'cancelled');
                })->with('task', function ($query) use ($search_key) {
                    return $query->where('title_en', 'like', '%' . $search_key . '%')->orWhere('title_bn', 'like', '%' . $search_key . '%')
                        ->where('task_status', '!=', 'completed')->where('task_status', '!=', 'cancelled')->get();
                })
                ->orderBy('id', 'DESC')->paginate($request->per_page ?? 10);
            $task_assignees = $task_assignees ? $task_assignees->toArray() : [];
            $route = 'tasks.search.assigned.task-info';

            return view('tasks.search.assigned.user_info', compact('task_assignees', 'search_key', 'route'));
        } catch (\Exception $exception) {
            return response()->json(responseFormat('error', $exception->getMessage()));
        }
    }

    public function searchAssignedTaskByDatetimeRange(Request $request)
    {
        try {
            $search_key = $request->date_time_range;
            $date_time_range = explode(' - ', $request->date_time_range);
            $start_date_time = $date_time_range[0];
            $end_date_time = $date_time_range[1];

            $start_date_time = Carbon::createFromFormat('d/m/Y H:i A', $start_date_time)->format('Y-m-d H:i:s');
            $end_date_time = Carbon::createFromFormat('d/m/Y H:i A', $end_date_time)->format('Y-m-d H:i:s');

            $task_assignees = TaskUser::where('assigner_officer_id', $this->getOfficerId())
                ->whereHas('task', function ($query) use ($start_date_time, $end_date_time) {
                    return $query->where('task_start_date_time', '>=', $start_date_time)->where('task_end_date_time', '<=', $end_date_time)
                        ->where('task_status', '!=', 'completed')->where('task_status', '!=', 'cancelled');
                })->with('task', function ($query) use ($start_date_time, $end_date_time) {
                    return $query->where('task_start_date_time', '>=', $start_date_time)->where('task_end_date_time', '<=', $end_date_time)
                        ->where('task_status', '!=', 'completed')->where('task_status', '!=', 'cancelled')->get();
                })
                ->paginate(10);
            $task_assignees = $task_assignees ? $task_assignees->toArray() : [];
            $route = 'tasks.search.assigned.datetime-range';
            return view('tasks.search.assigned.user_info', compact('task_assignees', 'search_key', 'route'));
        } catch (\Exception $exception) {
            return response()->json(responseFormat('error', $exception->getMessage()));
        }
    }

    public function assignedUser(Request $request)
    {
        $task_assignees = (new TaskServices())->assignedTaskToOthers($this->getOfficerId());
        $task_assignees = isSuccess($task_assignees) ? $task_assignees['data'] : [];
        $route = 'tasks.user.assigned';
        return view('tasks.search.assigned.user_info', compact('task_assignees', 'route'));
    }

    function unAssignUser(Request $request, TaskServices $taskServices)
    {
        try {

            $data = \Validator::make($request->all(), [
                'task_user_id' => 'required',
            ])->validate();

            $un_assign_user = $taskServices->unAssignUser($data);

            if (isSuccess($un_assign_user)) {
                return response()->json(responseFormat('success', $un_assign_user['data']));
            } else {
                return response()->json(responseFormat('error', $un_assign_user['data']));
            }

        } catch (\Exception $exception) {
            return response()->json(responseFormat('error', $exception->getMessage()));
        }
    }

    public function pendingTask(Request $request, TaskServices $taskServices)
    {
        $request->merge(['officer_id' => $this->getOfficerId()]);
        $request->merge(['page' => $request->page ?: 1]);
        $pending_tasks = $taskServices->pendingTasks($request);
        $pending_tasks = isSuccess($pending_tasks) ? $pending_tasks['data'] : [];
        return view('tasks.daily_dashboard.pending_task', compact('pending_tasks'));
    }

    public function updateComment(Request $request): JsonResponse
    {
        try {
            TaskUser::where('user_officer_id', $this->getOfficerId())->where('task_id', $request->task_id)->update(['comments' => $request->task_comment]);
            return response()->json(responseFormat('success', 'Successfully Added Comment'));
        } catch (\Exception $exception) {
            return response()->json(responseFormat('error', $exception->getMessage()));
        }
    }

    public function assignTaskPanel(Request $request)
    {
        try {
            $user_officer_id = $this->getOfficerId();
            $task_ids = $request->tasks;
            $tasks = Task::whereIn('id', $task_ids)->with('task_users')->get();
//            dd($tasks->toArray());
            return view('tasks.assign_task_panel', compact('user_officer_id', 'task_ids', 'tasks'));
        } catch (\Exception $exception) {
            return response()->json(responseFormat('error', $exception->getMessage()));
        }
    }

    public function assignTaskMultiple(Request $request, TaskServices $taskServices)
    {
        try {
            $data = \Validator::make($request->all(), [
                'tasks' => 'required',
                'task_assignees' => 'required',
                'assigner_officer_id' => 'required',
            ])->validate();
            $assign = $taskServices->assignTaskMultiple($data);
            if (isSuccess($assign, 'status', 'error')) {
                throw new \Exception($assign['message']);
            } else {
                return response()->json(responseFormat('success', 'Successfully assigned'));
            }
        } catch (\Exception $exception) {
            return response()->json(responseFormat('error', $exception->getMessage()));
        }
    }

    function getTaskUsers(Request $request, TaskServices $taskServices)
    {
        try {
            $data = \Validator::make($request->all(), [
                'task_id' => 'required',
            ])->validate();

            $task_all_users = $taskServices->getTaskUsers($data);

            if (isSuccess($task_all_users, 'status', 'error')) {
                throw new \Exception($task_all_users['message']);
            }
            $task_all_users = $task_all_users['data'];
            return view('tasks.users_list_panel', compact('task_all_users'));
        } catch (\Exception $exception) {
            return response()->json(responseFormat('error', $exception->getMessage()));
        }
    }

}

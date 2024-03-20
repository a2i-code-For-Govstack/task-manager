<?php

namespace App\Http\Controllers;

use App\Services\CalEventServices;
use App\Services\TaskServices;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        if (\Auth::check() && \Auth::user()) {
            return redirect()->route('admin.index');
        } else {
            return redirect()->route('home.dashboard');
        }
    }

    public function dashboard(Request $request)
    {
        return view('modules.dashboard.index');
    }

    public function dailyDashboard(Request $request, CalEventServices $calEventServices, TaskServices $taskServices)
    {
        $request->merge(['officer_id' => $this->getOfficerId()]);
        $request->merge(['page' => 1]);

        $pending_tasks = $taskServices->pendingTasks($request);

        $task_assignees = $taskServices->getTaskAssignees($request);

        $task_assignees = $task_assignees ? $task_assignees->toArray() : [];

        $pending_tasks = isSuccess($pending_tasks) ? $pending_tasks['data'] : [];

        return view('dashboard.daily', compact('pending_tasks', 'task_assignees'));
    }

    public function getTaskAssignees(Request $request, TaskServices $taskServices)
    {
        $request->merge(['officer_id' => $this->getOfficerId()]);
        $request->merge(['page' => 1]);
        $task_assignees = $taskServices->getTaskAssignees($request);

        $task_assignees = $task_assignees ? $task_assignees->toArray() : [];

        return view('tasks.search.assigned.user_info', compact('task_assignees'));

    }
}

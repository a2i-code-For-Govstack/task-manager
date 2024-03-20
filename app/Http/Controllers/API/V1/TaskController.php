<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskUser;
use App\Services\TaskServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request, TaskServices $taskServices)
    {
        $tasks = $taskServices->allTasks($request);
        return response()->json($tasks);
    }

    public function getTaskDetails(Request $request, TaskServices $taskServices): JsonResponse
    {
        $data = \Validator::make($request->all(), ['task_id' => 'required|integer'])->validated();
        $details = $taskServices->taskDetails($data);
        return response()->json($details);
    }

    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request, TaskServices $taskServices): JsonResponse
    {
        $store_task = $taskServices->storeTask($request);
        return response()->json($store_task);
    }

    /**
     * Display the specified resource.
     *
     * @param Task $task
     * @return void
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Task $task
     * @return void
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Task $task
     * @return Response
     */
    public function update(Request $request, TaskServices $taskServices): JsonResponse
    {
        $store_task = $taskServices->updateTask($request);
        return response()->json($store_task);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Task $task
     * @return JsonResponse
     */
    public function destroy(Request $request, TaskServices $taskServices)
    {
        \Validator::make($request->all(), ['task_id' => 'required|integer'])->validate();
        $delete_task = $taskServices->deleteTask($request->task_id);
        return response()->json($delete_task);
    }

    public function pending(Request $request, TaskServices $taskServices): JsonResponse
    {
        $pending_tasks = $taskServices->pendingTasks($request);
        return response()->json($pending_tasks);
    }

    public function daily(Request $request, TaskServices $taskServices): JsonResponse
    {
        $daily_tasks = $taskServices->dailyTasks($request);
        return response()->json($daily_tasks);
    }

    public function updateTaskStatus(Request $request, TaskServices $taskServices): JsonResponse
    {
        $updated_status = $taskServices->updateTaskStatus($request);
        return response()->json($updated_status);
    }

    public function assignTaskToOther(Request $request, TaskServices $taskServices): JsonResponse
    {
        $assignee_data = \Validator::make($request->all(), ['task_id' => 'required|integer', 'task_assignee' => 'required|json', 'assigner_officer_id' => 'required|integer'])->validate();
        $task_data = [
            'task_id' => $assignee_data['task_id'],
            'task_assignee' => $assignee_data['task_assignee'],
            'organizer' => [
                'user_officer_id' => $assignee_data['assigner_officer_id'],
            ],
        ];
        $assign_tasks = $taskServices->assignTask($task_data);
        return response()->json($assign_tasks);
    }

    public function assignTaskToOtherMultiple(Request $request, TaskServices $taskServices): JsonResponse
    {
        $assignee_data = \Validator::make($request->all(), ['tasks' => 'required', 'task_assignee' => 'required|json'])->validate();
        $assign_tasks = $taskServices->assignTaskMultiple($assignee_data);
        return response()->json($assign_tasks);
    }

    public function getTaskUsers(Request $request, TaskServices $taskServices)
    {
        $assignee_data = \Validator::make($request->all(), ['task_id' => 'required|integer', 'user_type' => 'nullable|string'])->validate();
        $assign_users = $taskServices->getTaskUsers($assignee_data);
        return response()->json($assign_users);
    }

    public function assignedTaskToOthers(Request $request, TaskServices $taskServices): JsonResponse
    {
        $assignee_data = \Validator::make($request->all(), ['officer_id' => 'required|integer'])->validate();
        $assign_tasks = $taskServices->assignedTaskToOthers($assignee_data);
        return response()->json($assign_tasks);
    }

    public function updateComment(Request $request): JsonResponse
    {
        try {
            TaskUser::where('user_officer_id', $request->officer_id)->where('task_id', $request->task_id)->update(['comments' => $request->task_comment]);
            return response()->json(responseFormat('success', 'Successfully Added Comment'));
        } catch (\Exception $exception) {
            return response()->json(responseFormat('error', $exception->getMessage()));
        }
    }
}

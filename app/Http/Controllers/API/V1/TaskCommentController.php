<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Services\TaskCommentServices;
use Illuminate\Http\Request;

class TaskCommentController extends Controller
{
    public function getTaskUsers(Request $request)
    {
        try {
            $task_users = (new TaskCommentServices())->getTaskCommentUsers($request->task_id, $request->officer_id);
            if (isSuccess($task_users, 'status', 'error')) {
                throw new \Exception($task_users['message']);
            }
            return response()->json($task_users);
        } catch (\Exception $exception) {
            return response()->json(responseFormat('error', $exception->getMessage()));
        }
    }

    public function getComments(Request $request, TaskCommentServices $commentServices)
    {
        try {
            \Validator::make($request->all(), ['task_id'=>'required|integer','user_officer_id' => 'required|integer', 'selected_officer_id' => 'required|integer'])->validated();
            $task_comments = $commentServices->getComments($request);
            if (isSuccess($task_comments, 'status', 'error')) {
                throw new \Exception($task_comments['message']);
            }
            return response()->json($task_comments);
        } catch (\Exception $exception) {
            return response()->json(responseFormat('error', $exception->getMessage()));
        }
    }

    public function saveComment(Request $request, TaskCommentServices $commentServices)
    {
        try {
            \Validator::make($request->all(), [
                'task_id' => 'required|integer',
                'sender_officer_id' => 'required|integer',
                'sender_name_en' => 'required|string',
                'sender_name_bn' => 'required|string',
                'receiver_officer_id' => 'required|integer',
                'receiver_name_en' => 'required|string',
                'receiver_name_bn' => 'required|string',
                'comment' => 'required|string',
            ])->validated();
            $save_comment = $commentServices->saveTaskComment($request);
            if (isSuccess($save_comment, 'status', 'error')) {
                throw new \Exception($save_comment['message']);
            }
            return response()->json($save_comment);
        } catch (\Exception $exception) {
            return response()->json(responseFormat('error', $exception->getMessage()));
        }
    }
}

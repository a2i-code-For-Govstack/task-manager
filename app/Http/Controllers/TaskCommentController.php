<?php

namespace App\Http\Controllers;

use App\Services\TaskCommentServices;
use Illuminate\Http\Request;

class TaskCommentController extends Controller
{
    public function loadCommentPanel(Request $request)
    {
        $user_officer_id = $request->officer_id ?: $this->getOfficerId();
        $task_id = $request->task_id;
        $task_users = (new TaskCommentServices())->getTaskCommentUsers($task_id, $user_officer_id);
        if (isSuccess($task_users)) {
            $task_users = $task_users['data'];
            return view('tasks.comments.comments_panel', compact('task_users', 'user_officer_id', 'task_id'));
        } else {
            return response()->json($task_users);
        }
    }

    public function getComments(Request $request, TaskCommentServices $commentServices)
    {
        $user_officer_id = $this->getOfficerId();
        $selected_officer_id = $request->selected_officer_id;
        $request->merge(['user_officer_id' => $user_officer_id]);
        $task_comments = $commentServices->getComments($request);

        if (isSuccess($task_comments)) {
            $task_comments = $task_comments['data'];
            return view('tasks.comments.all_comments', compact('task_comments', 'user_officer_id', 'selected_officer_id'));
        } else {
            return response()->json($task_comments);
        }
    }

    public function saveComment(Request $request, TaskCommentServices $commentServices)
    {
        $request->merge(['sender_name_en' => $this->current_desk()['officer_en']]);
        $request->merge(['sender_name_bn' => $this->current_desk()['officer_bn']]);
        $save_comment = $commentServices->saveTaskComment($request);
        return response()->json($save_comment);
    }
}

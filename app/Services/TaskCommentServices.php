<?php

namespace App\Services;

use App\Models\TaskComment;
use App\Models\TaskUser;
use App\Traits\ApiHeart;
use App\Traits\FireNotification;
use App\Traits\UserInfoCollector;
use Exception;
use Illuminate\Http\Request;

class TaskCommentServices
{
    use UserInfoCollector, ApiHeart, FireNotification;

    public function getTaskCommentUsers($task_id, $officer_id)
    {
        try {
            $current_task_user = TaskUser::where('task_id', $task_id)->where('user_officer_id', $officer_id)->first();
            if ($current_task_user->user_type == 'organizer') {
                $task_users = TaskUser::where('task_id', $task_id)->where('user_officer_id', '!=', $officer_id)->get();
            } else {
                $task_users = TaskUser::where('task_id', $task_id)->where('user_type', 'organizer')->get();
            }
            return responseFormat('success', $task_users);
        } catch (\Exception $exception) {
            return responseFormat('error', $exception->getMessage());
        }
    }

    public function getComments(Request $request): array
    {
        try {
            $task_comments = TaskComment::where('task_id', $request->task_id)->where(function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $query->where('sender_officer_id', $request->selected_officer_id)->where('receiver_officer_id', $request->user_officer_id);
                })->orWhere(function ($query) use ($request) {
                    $query->where('sender_officer_id', $request->user_officer_id)->where('receiver_officer_id', $request->selected_officer_id);
                });
            });
            $task_comments = $task_comments->get()->toArray();
            return responseFormat('success', $task_comments);
        } catch (\Exception $exception) {
            return responseFormat('error', $exception->getMessage());
        }
    }

    public function saveTaskComment(Request $request): array
    {
        try {
            $receiver_data = TaskUser::where('task_id', $request->task_id)->where('user_officer_id', $request->receiver_officer_id)->first();
            if (!$receiver_data) {
                throw new Exception('Receiver Not Found!');
            }
            $comment_data = [
                'task_id' => $request->task_id,
                'sender_officer_id' => $request->sender_officer_id,
                'sender_name_en' => $request->sender_name_en,
                'sender_name_bn' => $request->sender_name_bn,
                'receiver_officer_id' => $request->receiver_officer_id,
                'receiver_name_en' => $receiver_data->user_name_en,
                'receiver_name_bn' => $receiver_data->user_name_bn,
                'comment' => $request->comment,
            ];

            $save = TaskComment::create($comment_data);
            return [
                'status' => 'success',
                'data' => $save,
                'message' => 'Comment Saved Successfully!'
            ];
        } catch (\Exception $exception) {
            return responseFormat('error', $exception->getMessage());
        }
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskComment extends Model
{
    protected $fillable = [
        'task_id',
        'sender_officer_id',
        'sender_name_en',
        'sender_name_bn',
        'receiver_officer_id',
        'receiver_name_en',
        'receiver_name_bn',
        'comment',
    ];
}

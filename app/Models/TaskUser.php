<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskUser extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'task_id',
        'user_email',
        'user_name_en',
        'user_name_bn',
        'visibility_type',
        'username',
        'user_phone',
        'user_officer_id',
        'user_designation_id',
        'user_office_id',
        'user_office_name_en',
        'user_office_name_bn',
        'user_unit_id',
        'user_office_unit_name_en',
        'user_office_unit_name_bn',
        'user_designation_name_en',
        'user_designation_name_bn',
        'user_type',
        'task_user_status',
        'has_event',
        'has_assignees',
        'comments',
        'assigner_officer_id',
        'acceptance_status',
        'remarks',
    ];

    public function task(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id', 'id');
    }

    public function user_notification_setting(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserNotificationSetting::class, 'officer_id', 'user_officer_id');
    }
}

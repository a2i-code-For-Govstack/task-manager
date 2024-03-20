<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'title_en',
        'title_bn',
        'description',
        'task_start_date',
        'task_end_date',
        'task_start_date_time',
        'task_end_date_time',
        'parent_task_id',
        'has_event',
        'meta_data',
        'organization_id',
        'organization_name_en',
        'organization_name_bn',
        'location',
        'application_id',
        'application_name_en',
        'application_name_bn',

        'system_type',
        'task_status',
        'status',
    ];

    protected $appends = [
        'start_time', 'end_time',
    ];

    public function task_users(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TaskUser::class, 'task_id', 'id');
    }

    public function task_users_without_organizer(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TaskUser::class, 'task_id', 'id');
    }

    public function task_user(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(TaskUser::class, 'task_id', 'id');
    }

    public function user_task_notifications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TaskNotification::class, 'task_id', 'id');
    }

    public function task_organizer(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(TaskUser::class, 'task_id', 'id')->where('user_type', 'organizer');
    }

    public function getStartTimeAttribute($value): ?string
    {
        if ($this->task_start_date_time) {
            return Carbon::createFromFormat('Y-m-d H:i:s', $this->task_start_date_time)->format('h:i A');
        }
        return null;
    }

    public function getEndTimeAttribute($value): ?string
    {
        if ($this->task_end_date_time) {
            return Carbon::createFromFormat('Y-m-d H:i:s', $this->task_end_date_time)->format('h:i A');
        }
        return null;
    }

    public function sub_tasks()
    {
        return $this->hasMany(Task::class, 'parent_task_id', 'id');
    }

}

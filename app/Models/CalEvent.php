<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CalEvent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'event_title_en',
        'event_title_bn',
        'all_day',
        'organization_id',
        'organization_name_en',
        'organization_name_bn',

        'application_id',
        'application_name_en',
        'application_name_bn',

        'event_description',
        'event_start_date_time',
        'event_end_date_time',
        'event_start_date',
        'event_end_date',
        'event_start_time',
        'event_end_time',
        'event_location',
        'parent_event_id',
        'task_id',
        'event_type',
        'event_visibility',
        'event_previous_link',
        'status',
        'recurrence',
        'recurrent_cal_id',
    ];

    public function setEventStartTimeAttribute($time)
    {
        $this->attributes['event_start_time'] = \DateTime::createFromFormat('H:i A', $time) ? \DateTime::createFromFormat('H:i A', $time)->format('H:i:s') : $time;
    }

    public function setEventEndTimeAttribute($time)
    {
        $this->attributes['event_end_time'] = \DateTime::createFromFormat('H:i A', $time) ? \DateTime::createFromFormat('H:i A', $time)->format('H:i:s') : $time;
    }

    public function setEventStartDateAttribute($date)
    {
        if (strstr($date, '/')) {
            $date = str_replace('/', '-', $date);
        }

        $this->attributes['event_start_date'] = Carbon::parse($date)->format('Y-m-d');
    }

    public function setEventEndDateAttribute($date)
    {
        if (strstr($date, '/')) {
            $date = str_replace('/', '-', $date);
        }

        $this->attributes['event_end_date'] = Carbon::parse($date)->format('Y-m-d');
    }

    public function getEventStartDateAttribute($date)
    {
        Carbon::parse($date)->format('d-m-Y');
    }

    public function getEventEndDateAttribute($date): string
    {
        return Carbon::parse($date)->format('d-m-Y');
    }

    public function event_guests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CalEventGuest::class, 'event_id', 'id');
    }
}

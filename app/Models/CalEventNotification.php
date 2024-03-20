<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalEventNotification extends Model
{
    protected $fillable = [
        'event_id',
        'user_email',
        'user_officer_id',
        'user_designation_id',
        'username',
        'event_notification',
        'unit',
        'interval',
        'notification_medium',
        'is_dispatched',
    ];
}

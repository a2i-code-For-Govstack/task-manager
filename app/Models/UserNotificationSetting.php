<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotificationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'officer_id',
        'web_pusher',
        'email',
        'sms',
        'mobile_pusher',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CalEventGuest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'event_id',
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
        'tag_color',
        'user_duration',
        'acceptance_status',
        'remarks',
    ];

    public function event(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CalEvent::class, 'event_id', 'id');
    }
}

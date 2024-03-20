<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalEventPreferredGuest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_email',
        'username',
        'preferred_email',
        'preferred_name_en',
        'preferred_name_bn',
        'preferred_username',
        'preferred_record_id',
        'preferred_office_id',
        'preferred_office_name_en',
        'preferred_office_name_bn',
        'preferred_unit_id',
        'preferred_office_unit_name_en',
        'preferred_office_unit_name_bn',
        'preferred_designation_name_en',
        'preferred_designation_name_bn',
    ];
}

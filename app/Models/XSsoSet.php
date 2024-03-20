<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XSsoSet extends Model
{
    use HasFactory;

    protected $fillable = [
        'sso_name',
        'sso_login_url',
        'sso_logout_url',
        'sso_api_url',
        'is_custom',
        'is_active',
        'created_by',
        'updated_by',
    ];
}

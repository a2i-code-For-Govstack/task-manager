<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SystemAuthenticate extends \Illuminate\Auth\Middleware\Authenticate
{
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            return route('login');
        }
    }
}

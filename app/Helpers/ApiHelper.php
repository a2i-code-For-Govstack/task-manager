<?php

namespace App\Helpers;

class ApiHelper
{
    public static function api_url($name = '', $url = '')
    {
        if ($url || $name) {
            \Config::set('constants.api_url', $url);
            \Config::set('constants.client_api_name', $name);
            return config('constants.api_url');
        }
        return config('constants.api_url');
    }

    public static function config_clear()
    {
//        \Artisan::call('config:cache');
    }

}

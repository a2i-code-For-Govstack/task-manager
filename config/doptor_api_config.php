<?php

return [
    'client_id' => env('NDOPTOR_CLIENT_ID', ''),
    'client_pass' => env('NDOPTOR_CLIENT_PASSWORD', ''),
    'client_login_url' => env('NDOPTOR_API_URL', '') . 'api/client/login',
    'upload_profile_picture' => env('NDOPTOR_API_URL', '') . 'api/user/upload-profile-picture/',
    'user_image' => env('NDOPTOR_API_URL', '') . 'api/user/images/',
    'doptor_admin' => env('NDOPTOR_ADMIN', ''),
    'user_profile_url' => env('NDOPTOR_ADMIN', '') . 'profile',
    'doptor_api_url' => env('NDOPTOR_API_URL', ''),
];

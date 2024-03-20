<?php
return [
    'tag_colors' => [
        'fc fc-event-success' => 'bg-success',
        'fc fc-event-warning' => 'bg-waring',
        'fc fc-event-danger' => 'bg-danger',
        'fc fc-event-primary' => 'bg-primary',
        'fc fc-event-info' => 'bg-info',
    ],
    'client_id' => 'calendar-app',
    'client_pass' => 'calendar123',
    'secret_key' => env('SECRET_KEY', 'EC4BA433C982762E1B86DB4E3433D'),

    'api_url' => env('API_URL', ''),
    'client_api_name' => 'ndoptor',
    'app_name' => 'Doptor | Task Manager',
];

<?php
return [
    'push_notifier_app_mode' => env('PUSH_NOTIFIER_APP_MODE', 10),
    'push_notifier_url' => env('PUSH_NOTIFIER_URL', 'https://notifier.tappware.com'),
    'push_notifier_authorization_url' => env('PUSH_NOTIFIER_URL', 'https://notifier.tappware.com') . '/pusher/beams-auth',
    'push_notifier_publish' => env('PUSH_NOTIFIER_URL', 'https://notifier.tappware.com') . '/publish/private',
    'emailer_url' => env('EMAILER_URL', 'https://dev-mailer-nothi-next.tappware.com/api/email_queues.json'),
];

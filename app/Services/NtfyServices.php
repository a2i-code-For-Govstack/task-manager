<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class NtfyServices
{
    public function initNtfy()
    {
        return Http::withBasicAuth(config('ntfy.username'), config('ntfy.password'));
    }

    public function dispatchToNtfy($title, $message, $recipient, $click_action = 'javascript:;')
    {
        $domain = config('ntfy.ntfy_url') . '/' . $recipient;
        $dispatch = $this->initNtfy()->withHeaders([
            'Title' => $title,
            'Click' => $click_action
        ])->post($domain, [
            'content' => $message
        ])->json();
        return $dispatch;
    }
}

<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait ApiHeart
{
    public function initHttpWithClientToken($username): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withoutVerifying()->withHeaders($this->apiHeaders())->withToken($this->getClientToken($username));
    }

    public function apiHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'api-version' => '1',
        ];
    }

    public function getClientToken($username)
    {
        $client_name = config('constants.client_api_name');
        $url = config('client_api_constants.ndoptor.api_url_root') . config('client_api_constants.ndoptor.client_login');
        $client_id = config('client_api_constants.ndoptor.client_id');
        $client_pass = config('client_api_constants.ndoptor.client_password');

        $getToken = $this->initHttp()->post($url, [
            'client_id' => $client_id,
            'password' => $client_pass,
            'username' => $username
        ]);

        if ($getToken->status() == 200 && $getToken->json()['status'] == 'success') {
            return $getToken->json()['data']['token'];
        } else {
            return $getToken->json();
        }
    }

    public function initHttp(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withoutVerifying()->withHeaders($this->apiHeaders());
    }
}


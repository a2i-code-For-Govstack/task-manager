<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Google\Client;

class GoogleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Client::class, function ($app) {
            $client = new Client();
            $client->setAuthConfig(storage_path('app/google-client-secret.json'));
            $client->addScope(\Google\Service\Calendar::CALENDAR);
            return $client;
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

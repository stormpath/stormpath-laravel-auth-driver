<?php

namespace Stormpath\Providers;

use Auth;
use Stormpath\Client;
use Stormpath\StormpathUserProvider;
use Illuminate\Support\ServiceProvider;
use Stormpath\Stormpath;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        Auth::extend('stormpath', function($app) {
            // Return an instance of Illuminate\Contracts\Auth\UserProvider...
            return new StormpathUserProvider($app['stormpath.client'], $app['stormpath.application']);
        });
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('stormpath.client', function ($app) {
            Client::$apiKeyProperties = "apiKey.id=".env('STORMPATH_ID')."\napiKey.secret=".env('STORMPATH_SECRET');
            return Client::getInstance();
        });

        $this->app->singleton('stormpath.application', function ($app) {
            $application = $app['stormpath.client']->get('applications/' . env('STORMPATH_APPLICATION'), STORMPATH::APPLICATION);
            return $application;
        });

    }
}
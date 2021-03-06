<?php

namespace Stormpath\Providers;

use Auth;
use Stormpath\Client;
use Stormpath\StormpathUserProvider;
use Illuminate\Support\ServiceProvider;
use Stormpath\Stormpath;
/**
 * @codeCoverageIgnore
 */
class AuthServiceProvider extends ServiceProvider
{
    const INTEGRATION_NAME = 'stormpath-laravel-auth-driver';
    const INTEGRATION_VERSION = '0.1.0-alpha.3';
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/Stormpath.php' => config_path('stormpath.php'),
        ]);

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
            Client::$apiKeyProperties = "apiKey.id=".config('stormpath.id')."\napiKey.secret=".config('stormpath.secret');
            Client::$integration = self::INTEGRATION_NAME."/".self::INTEGRATION_VERSION;
            return Client::getInstance();
        });

        $this->app->singleton('stormpath.application', function ($app) {
            $application = $app['stormpath.client']->get('applications/' . config('stormpath.application'), STORMPATH::APPLICATION);
            return $application;
        });

    }
}
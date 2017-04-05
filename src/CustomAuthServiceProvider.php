<?php

namespace TheLHC\CustomAuth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthManager;
use Illuminate\Support\ServiceProvider;

class CustomAuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Laravel ^5.2.* || ^5.3.* || ^5.4.*
        if (method_exists(AuthManager::class, 'provider')) {

            Auth::provider('custom-auth', function($app, array $config) {
                $model = $config['model'];
                return new CustomAuthUserProvider($this->app['hash'], $model);
            });

            Auth::extend('custom-auth', function($app, $name, array $config) {
                return new CustomSessionGuard(
                    $name,
                    Auth::createUserProvider($config['provider']),
                    $app->make('session.store')
                );
            });
        } else {
            // Laravel ^5.1.*
            Auth::extend('custom-auth', function($app) {
                $model = $app['config']->get('auth.model');
                return new CustomGuard(
                    new CustomAuthUserProvider($app['hash'], $model),
                    $app->make('session.store')
                );
            });
        }
    }

    public function register()
    {
        //
    }
}

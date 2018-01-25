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
                $provider = Auth::createUserProvider($config['provider']);

                $guard = new CustomSessionGuard($name, $provider, $app['session.store']);

                // When using the remember me functionality of the authentication services we
                // will need to be set the encryption instance of the guard, which allows
                // secure, encrypted cookie values to get generated for those cookies.
                if (method_exists($guard, 'setCookieJar')) {
                    $guard->setCookieJar($app['cookie']);
                }

                if (method_exists($guard, 'setDispatcher')) {
                    $guard->setDispatcher($app['events']);
                }

                if (method_exists($guard, 'setRequest')) {
                    $guard->setRequest($app->refresh('request', $guard, 'setRequest'));
                }

                return $guard;
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

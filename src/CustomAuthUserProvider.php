<?php

namespace TheLHC\CustomAuth;

use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Auth\EloquentUserProvider;

class CustomAuthUserProvider extends EloquentUserProvider implements UserProvider
{


    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(UserContract $user, array $credentials)
    {
        $plain = $credentials['password'];

        return ( hash( 'sha512', $plain . $user->salt ) == $user->password );
    }
}

<?php

namespace TheLHC\CustomAuth\Tests;

use TheLHC\CustomAuth\CustomAuthUserModel;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends CustomAuthUserModel implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;
    public $timestamps = false;

    protected $fillable = [
        'email',
        'name',
        'password'
    ];

    public function sendVerificationEmail()
    {
        //
    }

    public function sendNewEmailVerificationEmail()
    {
        //
    }

    public function notifyPasswordChange()
    {
        //
    }
}

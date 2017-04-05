<?php

namespace TheLHC\CustomAuth;

use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\Guard as GuardContract;

class CustomSessionGuard extends SessionGuard implements GuardContract
{
    use AdminUser;
}

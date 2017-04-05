<?php

namespace TheLHC\CustomAuth;

use Illuminate\Auth\Guard;
use Illuminate\Contracts\Auth\Guard as GuardContract;

class CustomGuard extends Guard implements GuardContract
{
    use AdminUser;
}

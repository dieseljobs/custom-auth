<?php

namespace TheLHC\CustomAuth;

trait AdminUser
{
    public function adminUser()
    {
        if (! session()->has('adminid')) return null;

        $model = $this->provider->getModel();
        $class = '\\'.ltrim($model, '\\');

        return (new $class)->find(session('adminid'));
    }
}

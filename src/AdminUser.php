<?php

namespace TheLHC\CustomAuth;

trait AdminUser
{

    /**
     * The currently authenticated admin user.
     *
     * @var \Illuminate\Contracts\Auth\Authenticatable
     */
    protected $adminUser;

    /**
     * Log the given admin user ID into the application without sessions or cookies.
     *
     * @param  mixed  $id
     * @return bool
     */
    public function adminOnceUsingId($id)
    {
        if (! is_null($user = $this->provider->retrieveById($id))) {
            $this->setAdminUser($user);

            return true;
        }

        return false;
    }

    /**
     * Set the current user.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return void
     */
    public function setAdminUser($user)
    {
        $this->adminUser = $user;
    }

    /**
     * Get the currently authenticated admin user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function adminUser()
    {
        if (! is_null($this->adminUser)) {
            return $this->adminUser;
        }

        return;
    }
}

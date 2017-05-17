<?php

namespace TheLHC\CustomAuth;

use Illuminate\Database\Eloquent\Model;

class CustomAuthUserModel extends Model
{
    /**
     * Overload Illuminate\Database\Eloquent\Model registerModelEvent
     * Register a model event with the dispatcher.
     *
     * @param  string  $event
     * @param  \Closure|string  $callback
     * @param  int  $priority
     * @return void
     */
    protected static function registerModelEvent($event, $callback, $priority = 0)
    {
        if (isset(static::$dispatcher)) {
            $name = get_called_class();

            static::$dispatcher->listen("eloquent.{$event}: {$name}", $callback, $priority);
        }
    }

    /**
     * Hook into model events
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        /**
         * Mutators when saving
         *
         * @var TheLHC\CustomAuth\CustomAuthUserModel
         */
        static::saving(function ($model) {
            // create salt hash (unless explicitly set)
            if (empty($model->salt)) {
                $model->setSalt();
            }
            // check for password set/update
            if ($model->isDirty('password')) {
                $model->setEncryptedPassword();
            }
            // check for email update
            if ($model->isDirty('new_email')) {
                $model->setResetEmail();
            }
            // check for password update
            if ($model->isDirty('new_password')) {
                $model->setNewPassword();
            }
        });

        /**
         * Fill default attributes when creating
         *
         * @var TheLHC\CustomAuth\CustomAuthUserModel
         */
        static::creating(function ($model) {
            // set verified
            if (empty($model->verified)) {
                $model->setVerified();
            }
            // set IP
            if (empty($model->ip)) {
                $model->setIp();
            }
        });

        /**
         * Callbacks when created
         *
         * @var TheLHC\CustomAuth\CustomAuthUserModel
         */
        static::saved(function ($model) {
            // send email verification if not verified
            if (
                ! $model->is_verified and
                method_exists($model, 'sendVerificationEmail')
            ) {
                //$model->sendVerificationEmail();
            }
            // send email verification if newemail added
            if (
                $model->isDirty('resetemail') and
                ! is_null($model->resetemail) and
                method_exists($model, 'sendNewEmailVerificationEmail')
            ) {
                $model->sendNewEmailVerificationEmail();
            }
            // notify password has changed
            if (
                $model->isDirty('password') and
                $model->getOriginal('password') and
                method_exists($model, 'notifyPasswordChange')
            ) {
                $model->notifyPasswordChange();
            }
        });
    }

    /**
     * Get the fillable attributes for the model.
     *
     * @return array
     */
    public function getFillable()
    {
        return array_merge(
            $this->fillable,
            ['salt', 'resetemail', 'new_email', 'new_password', 'verified', 'ip']
        );
    }

    /**
     * Get the hidden attributes for the model.
     *
     * @return array
     */
    public function getHidden()
    {
        return $this->hidden;
    }

    /**
     * Is user verified attribute accessor
     *
     * @param  mixed $value
     * @return boolean
     */
    public function getIsVerifiedAttribute($value)
    {
        return $this->verified == "yes";
    }

    protected function setSalt()
    {
        $salt = $this->generateHash();
        $this->setAttribute('salt', $salt);
    }

    protected function setEncryptedPassword()
    {
        $password = hash('sha512', $this->password . $this->salt);

        $this->setAttribute('password', $password);
    }

    protected function setResetEmail()
    {
        $this->setAttribute('resetemail', $this->new_email);
        unset($this->new_email);
    }

    protected function setNewPassword()
    {
        $new_password = hash('sha512', $this->new_password . $this->salt);
        $this->setAttribute('password', $new_password);
        unset($this->new_password);
    }

    protected function setVerified()
    {
        $this->setAttribute('verified', md5($this->generateHash()));
    }

    public function verify()
    {
        $this->setAttribute('verified', 'yes');
    }

    protected function setIp()
    {
        if (is_callable('client_ip')) {
            $ip = client_ip();
        } else {
            $ip = request()->ip();
        }
        $this->setAttribute('ip', $ip);
    }

    protected function generateHash()
    {
        return hash(
          'sha512',
          substr(
            str_shuffle(
              str_repeat('0123456789abcdefghijklmnopqrstuvwxyz',5)
            ),
            0,
            5
          ).mt_rand(1,999999)*memory_get_usage(true)
        );
    }
}

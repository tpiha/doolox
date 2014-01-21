<?php

// use Illuminate\Auth\UserInterface;
// use Illuminate\Auth\Reminders\RemindableInterface;
use Cartalyst\Sentry\Users\Eloquent\User as SentryUserModel;

class User extends SentryUserModel {

    /**
     * Get all owned Sites
     *
     * @return array
     */
    public function getSites()
    {
        return $this->belongsToMany('Site', 'site_user', 'user_id', 'site_id');
    }

}
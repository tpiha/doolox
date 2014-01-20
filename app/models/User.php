<?php

// use Illuminate\Auth\UserInterface;
// use Illuminate\Auth\Reminders\RemindableInterface;
use Cartalyst\Sentry\Users\Eloquent\User as SentryUserModel;

class User extends SentryUserModel {

    /**
     * Get all owned WPSites
     *
     * @return array
     */
    public function getWPSites()
    {
        return $this->belongsToMany('WPSite', 'user_wpsite', 'user_id', 'wpsite_id');
    }

}
<?php

class WPSite extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'wpsites';

    /**
     * Allow mass assignment without utilizing the fillable array
     *
     * @var boolean
     */
    public static $unguarded = true;


    /**
     * Remove updated_at
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Get all owners
     *
     * @return array
     */
    public function getUsers()
    {
        return $this->belongsToMany('User', 'user_wpsite', 'wpsite_id', 'user_id');
    }

}
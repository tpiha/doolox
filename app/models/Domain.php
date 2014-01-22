<?php

class Domain extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'domains';

    protected $fillable = array('user_id', 'url');

    public function getOwner()
    {
        return $this->belongsTo('User', 'user_id', 'id');
    }

    public function getSites()
    {
        return $this->hasMany('Site', 'domain_id', 'id');
    }

    public static function boot()
    {
        parent::boot();

        Domain::deleting(function($domain) {
            $sites = $domain->getSites()->get();
            foreach ($sites as $site) {
                $site->delete();
            }
        });
    }

}
<?php

class Site extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'sites';

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
        return $this->belongsToMany('User', 'site_user', 'site_id', 'user_id');
    }

    public function getOwner()
    {
        return $this->belongsTo('User', 'user_id', 'id');
    }

    public function getDomain()
    {
        return str_replace('/', '', str_replace('http://', '', $this->url));
    }

    public static function boot()
    {
        parent::boot();

        Site::deleting(function($site) {
            if ($site->local) {
                $dbname = 'user' . $site->getOwner()->first()->id . '_db' . $site->id;
                DooloxController::drop_database($dbname);
                unlink(base_path() . '/websites/' . $site->getDomain());
                $dirPath = base_path() . '/users/' . $site->getOwner()->first()->email . '/' . $site->getDomain();
                foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dirPath, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path) {
                    $path->isFile() ? unlink($path->getPathname()) : rmdir($path->getPathname());
                }
                rmdir($dirPath);
            }
        });
    }

}
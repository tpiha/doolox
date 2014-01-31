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

    public function getDomains()
    {
        return Domain::where('user_id', $this->id)->orWhere('system_domain', true);
    }

    public function getOwnedSites()
    {
        return Site::where('user_id', $this->id);
    }

    public static function boot()
    {
        parent::boot();

        User::creating(function($user) {
            try {
                mkdir(base_path() . '/users/' . $user->email);
            }
            catch (Exception $e) {
                Log::error("Couldn't create user dir for user: $user->email");
            }
        });

        User::deleting(function($user) {
            try {
                $dirPath = base_path() . '/users/' . $user->email;
                foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dirPath, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path) {
                    $path->isFile() ? unlink($path->getPathname()) : rmdir($path->getPathname());
                }
                rmdir($dirPath);
            }
            catch (Exception $e) {
                Log::error("Couldn't delete user dir for user: $user->email");
            }
            $sites = $user->getOwnedSites()->get();
            foreach ($sites as $site) {
                Site::destroy($site->id);
            }
            $domains = $user->getDomains()->get();
            foreach ($domains as $domain) {
                Domain::destroy($domain->id);
            }
        });
    }

}
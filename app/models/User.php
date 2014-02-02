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

    public function getOwnedDomains()
    {
        return Domain::where('user_id', $this->id);
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
            $domains = $user->getOwnedDomains()->get();
            foreach ($domains as $domain) {
                Domain::destroy($domain->id);
            }
        });
    }

    public function subscribe_to_newsletter() {
        $token = str_random(32);
        $email = $this->email;
        $sql = "mysql -u" . Config::get('database.connections.managemysql.username') . " -p" . Config::get('database.connections.managemysql.password') . " -D " . Config::get('doolox.mainwpdb') . " -e \"INSERT INTO  wp_newsletter ( id , email , name , surname , sex , status , created , token , feed , feed_time , country , list_1 , list_2 , list_3 , list_4 , list_5 , list_6 , list_7 , list_8 , list_9 , list_10 , list_11 , list_12 , list_13 , list_14 , list_15 , list_16 , list_17 , list_18 , list_19 , list_20 , profile_1 , profile_2 , profile_3 , profile_4 , profile_5 , profile_6 , profile_7 , profile_8 , profile_9 , profile_10 , profile_11 , profile_12 , profile_13 , profile_14 , profile_15 , profile_16 , profile_17 , profile_18 , profile_19 , profile_20 , referrer , http_referer , wp_user_id , ip , test , flow ) VALUES ( NULL ,  '" + $email + "',  '',  '',  'n',  'C', CURRENT_TIMESTAMP ,  '" + $token + "',  '0',  '0',  '',  '0',  '0',  '0',  '0',  '0',  '0',  '0',  '0',  '0',  '0',  '0',  '0',  '0',  '0',  '0',  '0',  '0',  '0',  '0',  '0',  '',  '',  '',  '',  '',  '',  '',  '',  '',  '',  '',  '',  '',  '',  '',  '',  '',  '',  '',  '',  '',  '',  '0',  '',  '0',  '0' )\"";
        Log::info($email . ' ' . $token . ' ' . $sql);
        exec($sql);
    }

}
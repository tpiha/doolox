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

    public function change_domain($domain)
    {
        $old_url = $this->url;
        $old_url = str_replace('http://', '', $old_url);
        $old_url = str_replace('https://', '', $old_url);
        if (substr($old_url, -1) == '/') {
            $old_url = substr($old_url, 0, -1);
        }

        $new_url = $domain;
        $old_path = base_path() . '/users/' . $this->getOwner()->first()->email . '/' . $old_url . '/';
        $new_path = base_path() . '/users/' . $this->getOwner()->first()->email . '/' . $new_url . '/';
        $old_link = base_path() . '/websites/' . $old_url;
        $new_link = base_path() . '/websites/' . $new_url;

        $domain = explode('.', $domain);
        $subdomain = $domain[0];
        $domain = $domain[1] . '.' . $domain[2];

        $d = Domain::where('url', $domain)->first();

        rename($old_path, $new_path);
        unlink($old_link);
        symlink($new_path, $new_link);

        $this->url = 'http://' . $new_url . '/';
        $this->domain_id = $d->id;
        $this->subdomain = $subdomain;
        $this->save();

        $this->db_replace($old_url, $new_url);
    }

    public function change_email($email) {
        $d = Domain::find($this->domain_id);
        $url = $this->subdomain . '.' . $d->url;

        $old_email = $this->getOwner()->first()->email;
        $new_email = $email;
        $old_path = base_path() . '/users/' . $old_email . '/';
        $new_path = base_path() . '/users/' . $new_email . '/';
        $link = base_path() . '/websites/' . $url;

        rename($old_path, $new_path);
        unlink($link);
        symlink($new_path . $url, $link);

        $this->db_replace($old_email, $new_email);
    }

    public function db_replace($old, $new)
    {
        $user = $this->getOwner()->first();
        $dbname = 'doolox' . $user->id . '_db' . $this->id;
        $file = base_path() . '/app/storage/' . $dbname . '.sql';
        exec('mysqldump ' . $dbname . ' -u' . Config::get('database.connections.managemysql.username') . ' -p' . Config::get('database.connections.managemysql.password') . ' > ' . $file);

        $str = file_get_contents($file);
        $str = str_replace("$old", "$new", $str);
        $str = Site::fix_serialized($str);
        file_put_contents($file, $str);

        exec('mysql ' . $dbname . ' -u' . Config::get('database.connections.managemysql.username') . ' -p' . Config::get('database.connections.managemysql.password') . ' < ' . $file);
        unlink($file);
    }

    /**
     * Fix a serialized string
     */
    public static function fix_serialized($string) {
        if ( !preg_match('/^[aOs]:/', $string) ) return $string;
        if ( @unserialize($string) !== false ) return $string;
        $string = preg_replace_callback('/\bs:(\d+):"(.*?)"/', Site::fix_str_length, $string);
        return $string;
    }

    /**
     * Callback function for replacing the string
     */
    public static function fix_str_length($matches) {
        $string = $matches[2];
        $right_length = strlen($string);
        return 's:' . $right_length . ':"' . $string . '"';
    }

    public static function boot()
    {
        parent::boot();

        Site::deleted(function($site) {
            if ($site->local) {
                $dbname = 'doolox' . $site->getOwner()->first()->id . '_db' . $site->id;
                try {
                    DooloxController::drop_database($dbname);
                }
                catch (Exception $e){
                    Log::error("Couldn't delete database: $dbname");
                }
                unlink(base_path() . '/websites/' . $site->getDomain());
                $dirPath = base_path() . '/users/' . $site->getOwner()->first()->email . '/' . $site->getDomain();
                try {
                    foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dirPath, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path) {
                        $path->isFile() ? unlink($path->getPathname()) : rmdir($path->getPathname());
                    }
                }
                catch (Exception $e) {
                    Log::error("Couldn't delete directory: $dirPath");
                }
                rmdir($dirPath);
            }
        });
    }

}
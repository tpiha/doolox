<?php

class Doolox {

    /**
     * Checks if domain is available (for domains sectin purposes)
     *
     * @param string $domain - domain to check
     * @return array ($available, $status)
     */
    public static function is_domain_available($domain)
    {
        $domain = explode('.', $domain);
        try {
            $subdomain = $domain[2];
            $tld = $subdomain;
            $subdomain = $domain[0];
            $domain = $domain[1];
        }
        catch (Exception $e) {
            $subdomain = '';
            try {
                $tld = $domain[1];
                $domain = $domain[0];
            }
            catch (Exception $e) {
                // no dots
                return array(false, 1);
            }
        }

        // check allowed characters
        if ($tld == Str::slug($tld) && $domain == Str::slug($domain) && $subdomain == Str::slug($subdomain)) {

            $domain = join(array($domain, $tld), '.');

            if ($domain != Config::get('doolox.system_domain')) {
                if (Domain::where('url', $domain)->count()) {
                    return array(false, 3);
                }
                else if (in_array($tld, array('com', 'net', 'org'))) {
                    if (self::namecom_is_available($domain)) {
                        Log::debug("Name.com - domain available: $domain");
                        return array(true, 0);
                    }
                    else {
                        return array(false, 2);
                    }
                }
                else {
                    $ip = gethostbyname($domain);
                    if ($ip == $domain) {
                        return array(false, 2);
                    }
                    else {
                        return array(true, 0);
                    }
                }                    
            }
            else {
                return array(false, 3);
            }
        }
        else {
            return array(false, 1);
        }
    }

    /**
     * Checks if domain is available over name.com service
     *
     * @param string $domain - domain to check
     * @return boolean
     */
    public static function namecom_is_available($domain)
    {
        require_once(base_path() . "/tools/namecom_api.php");
        $api = new NameComApi();
        $api->baseUrl(Config::get('doolox.namecom_url'));
        $api->username(Config::get('doolox.namecom_user'));
        $api->apiToken(Config::get('doolox.namecom_token'));
        $response = $api->check_domain($domain);
        return $response->domains->{$domain}->avail;
    }

    /**
     * Creates MySQL database
     *
     * @param string $database - database name
     * @param string $username - new user's username
     * @param string $password - new user's password
     * @return void
     */
    public static function create_database($database, $username, $password)
    {
        DB::connection('managemysql')->statement("CREATE DATABASE $database");
        DB::connection('managemysql')->statement("GRANT ALL ON $database.* TO '$username'@'localhost' IDENTIFIED BY '$password'");
        DB::connection('managemysql')->statement("FLUSH PRIVILEGES");
    }

    /**
     * Drops MySQL database
     *
     * @param string $dbname - database name
     * @return void
     */
    public static function drop_database($dbname)
    {
        DB::connection('managemysql')->statement("DROP USER $dbname@localhost");
        DB::connection('managemysql')->statement("DROP DATABASE $dbname");
        DB::connection('managemysql')->statement("FLUSH PRIVILEGES");
    }

    /**
     * Installs new WordPress instance on Doolox
     *
     * @param string $url - new website's url
     * @param string $title - new website's title
     * @param string $username - admin username for new website
     * @param string $password - admin password for new website
     * @param string $email - admin email for new website
     * @return void
     */
    public static function install_wordpress($url, $title, $username, $password, $email)
    {
        $data = array(
            'weblog_title' => $title,
            'user_name' => $username,
            'admin_password' => $password,
            'admin_password2' => $password,
            'admin_email' => $email,
            'blog_public' => 1,
        );
        $response = Requests::post('http://' . $url . '/wp-admin/install.php?step=2', array('timeout' => 90), $data);
    }

    /**
     * Prepares WordPress package for new installation
     *
     * @param User $user - User object
     * @param string $domain - domain with subdomain
     * @return void
     */
    public static function get_wordpress($user, $domain)
    {
        $source = base_path() . '/wordpress';
        $dest = base_path() . '/users/' . $user->email . '/' . $domain;

        mkdir($dest);

        foreach (
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST) as $item
            ) {
            if ($item->isDir()) {
                mkdir($dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            } else {
                copy($item, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }

        symlink($dest, base_path() . '/websites/' . $domain);
    }

    /**
     * Creates wp-config.php file for new website
     *
     * @param User $user - User object
     * @param string $domain - domain with subdomain
     * @param string $dbname - new website's database name / username
     * @param string $dbpass - new website's database password 
     * @return void
     */
    public static function create_wp_config($user, $domain, $dbname, $dbpass)
    {
        $source = base_path() . '/tools/wp-config.php';
        $dest = base_path() . '/users/' . $user->email . '/' . $domain . '/wp-config.php';
        $wpconfig = file_get_contents($source);

        $wpconfig = str_replace('###DB_NAME###', $dbname, $wpconfig);
        $wpconfig = str_replace('###DB_USER###', $dbname, $wpconfig);
        $wpconfig = str_replace('###DB_PASSWORD###', $dbpass, $wpconfig);
        $wpconfig = str_replace('###DB_HOST###', 'localhost', $wpconfig);

        $wpconfig = str_replace('###AUTH_KEY###', str_random(32), $wpconfig);
        $wpconfig = str_replace('###SECURE_AUTH_KEY###', str_random(32), $wpconfig);
        $wpconfig = str_replace('###LOGGED_IN_KEY###', str_random(32), $wpconfig);
        $wpconfig = str_replace('###LOGGED_IN_KEY###', str_random(32), $wpconfig);
        $wpconfig = str_replace('###AUTH_SALT###', str_random(32), $wpconfig);
        $wpconfig = str_replace('###SECURE_AUTH_SALT###', str_random(32), $wpconfig);
        $wpconfig = str_replace('###LOGGED_IN_SALT###', str_random(32), $wpconfig);
        $wpconfig = str_replace('###NONCE_SALT###', str_random(32), $wpconfig);

        file_put_contents($dest, $wpconfig);
    }

    /**
     * Creates wp-config.php file for new website
     *
     * @param string $url - WordPress website url
     * @param string $username - admin username
     * @param string $password - admin password
     * @return boolean - true if successfull, else false
     */
    public static function install_doolox_node($url, $username, $password)
    {
        Log::debug($url . ' ' . $username . ' ' . $password);
        $headers = array();
        $options = array();

        $session = new Requests_Session($url);

        $data = array('log' => $username, 'pwd' => $password);
        $request = $session->post('wp-login.php', array(), $data);

        if (strpos($request->url, 'wp-login.php') === false) {
            $request = $session->get('wp-admin/plugin-install.php?tab=search&s=doolox+node&plugin-search-input=Search+Plugins', $headers, $options);
            $start = strpos($request->body, 'update.php?action=install-plugin&amp;plugin=doolox-node');

            if ($start !== false) {
                $end = strpos($request->body, '"', $start);
                $length = $end - $start;
                $link = substr($request->body, $start, $length);
                $link = str_replace($url, '', $link);
                $link = str_replace('&amp;', '&', $link);
                $request = $session->get('wp-admin/' . $link, $headers, $options);

                $start = strpos($request->body, 'plugins.php?action=activate&amp;plugin=doolox-node%2Fdoolox.php');
                if ($start !== false) {
                    $end = strpos($request->body, '"', $start);
                    $length = $end - $start;
                    $link = substr($request->body, $start, $length);
                    $link = str_replace($url, '', $link);
                    $link = str_replace('&amp;', '&', $link);
                    $request = $session->get('wp-admin/' . $link, $headers, $options);

                    $request = $session->get('wp-admin/options-general.php?page=doolox-settings', $headers, $options);
                    $start = strpos($request->body, 'options.php?_wpnonce=');
                    $end = strpos($request->body, '"', $start);
                    $length = $end - $start;
                    $action = substr($request->body, $start, $length);

                    Log::debug($action . ' ' . $start . ' ' . $end);

                    $hidden = '<input type="hidden" id="_wpnonce" name="_wpnonce" value="';
                    $start = strpos($request->body, $hidden) + strlen($hidden);
                    $end = strpos($request->body, '"', $start);
                    $length = $end - $start;
                    $_wpnonce = substr($request->body, $start, $length);

                    Log::debug($_wpnonce . ' ' . $start . ' ' . $end);

                    $data = array(
                        'option_page' => 'doolox-settings',
                        'action' => 'update',
                        '_wpnonce' => $_wpnonce,
                        '_wp_http_referer' => '/wp-admin/options-general.php?page=doolox-settings',
                        'submit' => 'Save Changes',
                        'dooloxpkg' => Config::get('doolox.public_key')
                    );

                    $request = $session->post('wp-admin/' . $action, array(), $data);

                    return true;
                }
                else {
                    return false;
                }
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
    }

    /**
     * Calculate size of a folder
     *
     * @param string $dir - path to folder
     * @return integer - size in bytes
     */
    public static function folder_size($dir)
    {
        $count_size = 0;
        $count = 0;
        $dir_array = scandir($dir);
        foreach($dir_array as $key=>$filename) {
            if($filename!=".." && $filename!=".") {
                if(is_dir($dir."/".$filename)) {
                    $new_foldersize = self::folder_size($dir."/".$filename);
                    $count_size = $count_size + $new_foldersize;
                } else if(is_file($dir."/".$filename)) {
                    $count_size = $count_size + filesize($dir."/".$filename);
                    $count++;
                }
            }

        }
        return $count_size;
    }

    /**
     * Replace 'search' with 'replace' from one file and save i to another
     *
     * @param string $from - path to file to read from
     * @param string $to - path to file to save to
     * @param string $search - string to search for
     * @param string $replace - string to replace with
     * @return void
     */
    public static function replace_in_file($from, $to, $search, $replace)
    {
        $str = file_get_contents($from);
        $str = str_replace($search, $replace, $str);
        file_put_contents($to, $str);
    }

}
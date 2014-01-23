<?php

class DooloxController extends BaseController {

	public function dashboard()
    {
        $user = Sentry::getUser();
        $sites = $user->getSites()->get();
        // $sites = array();
        foreach ($sites as $site) {
            $site->password = self::rc4($user->key, Crypt::decrypt($site->password));
            $site->username = self::rc4($user->key, $site->username);
        }
		return View::make('dashboard')->with('sites', $sites);
	}

    public function site($id)
    {
        $validator = null;
        $site = Site::findOrFail((int) $id);

        if (Request::has('name')) {
            $rules = array(
                'name' => 'required',
                'url' => 'required',
            );
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->passes()) {
                $site->fill(Input::except('_token'));
                $site->save();
                Session::flash('success', 'Website successfully updated.');
                return Redirect::route('doolox.dashboard');
            }
        }

        return View::make('site')->with('site', $site)->withErrors($validator);
    }

    public function site_new()
    {
        $rules = array(
            'name' => 'required',
            'url' => 'required',
            'username' => 'required',
            'password' => 'required',
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->passes()) {
            $input = Input::except('_token');
            $input['password'] = Crypt::encrypt($input['password']);
            $site = Site::create($input);
            $user = Sentry::getUser();
            $user->getSites()->attach($site);
            Session::flash('success', 'New website successfully added.');
            return Redirect::route('doolox.dashboard');
        }

        Input::flash();

        return View::make('site_new')->withErrors($validator);
    }

    public function site_delete($id)
    {
        $site = Site::findOrFail((int) $id);
        $wpusersites = SiteUser::where('site_id', (int) $id)->get();
        foreach ($wpusersites as $wpusersite) {
            // die(var_dump($wpusersite->user_id));
            $wpusersite->delete();
        }
        $site->delete();
        Session::flash('success', 'Website successfully deleted.');
        return Redirect::route('doolox.dashboard');
    }

    public function site_rmuser($id, $user_id)
    {
        $site = Site::find($id);
        $site->getUsers()->detach($user_id);
        $site->save();
        Session::flash('success', 'User successfully removed from the website.');
        return Redirect::route('doolox.dashboard');
    }

    public function site_adduser($id)
    {
        if (Input::get('email')) {
            $user = User::where('email', Input::get('email'))->first();
            if ($user) {
                $user->getSites()->attach((int) $id);
                $user->save();
                Session::flash('success', 'User successfully added to the website.');
                return Redirect::route('doolox.dashboard');
            }
            else {
                Session::flash('error', 'There is no user with this email.');
                return Redirect::route('doolox.site', array('id' => $id));
            }
        }
        else {
            Session::flash('error', 'Email field is required.');
            return Redirect::route('doolox.site', array('id' => $id));
        }
    }

    public function site_install()
    {
        $domains = array();
        $_domains = Sentry::getUser()->getDomains()->get();
        $selected = 0;
        $selected_url = '';
        foreach ($_domains as $domain) {
            $domains["$domain->url"] = $domain->url;
            $selected_url = $domain->url;
        }
        // dd($domains);
        return View::make('site_install')->with(array('domains' => $domains, 'selected_url' => $selected_url));
    }

    public function site_install_post()
    {
        $domains = Sentry::getUser()->getDomains()->get();
        $domains_string = '';
        foreach ($domains as $domain) {
            $domains_string .= '.' . $domain->url . ',';
        }
        $rules = array(
            'url' => 'required|not_in:' . $domains_string,
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->passes()) {
            return Redirect::route('doolox.site_install_step2')->with(array('domain' => Input::get('domain'), 'url' => Input::get('url')));
        }
        $_domains = array();
        foreach ($domains as $domain) {
            $_domains["$domain->url"] = $domain->url;
            $selected_url = $domain->url;
        }

        Input::flash();

        return View::make('site_install')->withErrors($validator)->with(array('domains' => $_domains, 'selected_url' => $selected_url));
    }

    public function site_install_step2()
    {
        $domain = Input::get('domain');
        $url = Input::get('url');

        $rules = array(
            'url' => 'required|not_in:' . Config::get('doolox.system_domain') . ',',
            'title' => 'required',
            'username' => 'required',
            'email' => 'required|email',
            'password1' => 'required|same:password2',
            'password2' => 'required|same:password1',
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->passes()) {
            $title = Input::get('title');
            $username = Input::get('username');
            $password = Input::get('password1');
            $email = Input::get('email');

            $subdomain = str_replace($domain, '', $url);
            $d = Domain::where('url', $domain)->first();

            $user = Sentry::getUser();
            $site = Site::create(array('user_id' => $user->id, 'name' => $title, 'url' => 'http://' . $url . '/', 'username' => $username, 'password' => Crypt::encrypt($password), 'local' => true, 'admin_url' => '', 'domain_id' => $d->id));
            $user->getSites()->attach($site);

            self::get_wordpress(Sentry::getUser(), $url);
            $dbname = 'doolox' . $user->id . '_db' . $site->id;
            $dbpass = str_random(32);
            self::create_database($dbname, $dbname, $dbpass);
            self::create_wp_config($user, $url, $dbname, $dbpass);
            self::install_wordpress($url, $title, $username, $password, $email);
            Session::flash('success', 'New Doolox website successfully installed.');
            return Redirect::route('doolox.dashboard');
        }

        Input::flash();

        return View::make('site_install_step2')->with(array('domain' => $domain, 'url' => $url))->withErrors($validator);
    }

    /*
     * RC4 symmetric cipher encryption/decryption
     *
     * @param string key - secret key for encryption/decryption
     * @param string str - string to be encrypted/decrypted
     * @return string
     */
    public static function rc4($key, $pt)
    {
        $s = array();
        for ($i=0; $i<256; $i++) {
            $s[$i] = $i;
        }
        $j = 0;
        $x;
        for ($i=0; $i<256; $i++) {
            $j = ($j + $s[$i] + ord($key[$i % strlen($key)])) % 256;
            $x = $s[$i];
            $s[$i] = $s[$j];
            $s[$j] = $x;
        }
        $i = 0;
        $j = 0;
        $ct = '';
        $y;
        for ($y=0; $y<strlen($pt); $y++) {
            $i = ($i + 1) % 256;
            $j = ($j + $s[$i]) % 256;
            $x = $s[$i];
            $s[$i] = $s[$j];
            $s[$j] = $x;
            $ct .= $pt[$y] ^ chr($s[($s[$i] + $s[$j]) % 256]);
        }
        return bin2hex($ct);
    }

    public static function generate_key($length = 32)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $string = '';

        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[mt_rand(0, strlen($characters) - 1)];
        }

        return $string;
    }

    public function get_key()
    {
        $user = Sentry::getUser();
        return Response::json(array('key' => $user->key));
    }

    public function check_domain($domain)
    {
        return Response::json(array('free' => false, 'status' => 2));
    }

    public static function is_domain_available($domain, $user)
    {
        return true;
    //     $taken = array('blog', 'wiki', 'admin', '');
    //     $domain = explode('.', $domain);
    //     try {
    //         $subdomain = $domain[2];
    //         $tld = $subdomain;
    //         $subdomain = $domain[0];
    //         $domain = $domain[1];
    //     }
    //     catch {
    //         $subdomain = '';
    //         try {
    //             $tld = $domain[1];
    //             $domain = $domain[0];
    //         }
    //         catch {
    //             return array(false, 1);
    //         }
    //     }
    //     $domain = join(array($domain, $tld));

    //     if (DooloxController::is_valid_host($domain) && $subdomain == Str::slug($subdomain)) {
    //         // system domain
    //         if ($domain == Config::get('doolox.system_domain')) {
    //             if (in_array($subdomain, $taken) && !$user->isSuperUser()) {
    //                 return array(false, 4);
    //             }
    //             if () {

    //             }
    //             else {
    //                 return array(true, 0);
    //             }
    //         }
    //         // not system, but in database
    //         else if (Domain.:where('url', $domain)) {
    //         }
    //         // not system, not in database, com, net, org
    //         else if (in_array($tld, array('com', 'net', 'org'))) {
    //             if (self::namecom_is_available($domain)) {
    //                 return array(true, 0);
    //             }
    //             else {
    //                 return array(false, 2);
    //             }
    //         }
    //         // other top level domains
    //         else {
    //             $ip = gethostbyname($domain);
    //             if ($ip == $domain) {
    //                 return array(false, 2);
    //             }
    //             else {
    //                 return array(true, 0);
    //             }
    //         }
    //     }
    //     else {
    //         return array(false, 1);
    //     }
    }

    public static function namecom_is_available($domain)
    {
        require_once(base_path() . "/tools/namecom_api.php");
        $api = new NameComApi();
        $api->login(Config::get('doolox.namecom_user'), Config::get('doolox.namecom_token'));
        $response = $api->check_domain($domain);
        return $response;
    }

    public static function is_valid_host()
    {
        return true;
    }

    public static function create_database($database, $username, $password)
    {
        DB::connection('managemysql')->statement("CREATE DATABASE $database");
        DB::connection('managemysql')->statement("GRANT ALL ON $database.* TO '$username'@'localhost' IDENTIFIED BY '$password'");
        DB::connection('managemysql')->statement("FLUSH PRIVILEGES");
    }

    public static function drop_database($dbname)
    {
        DB::connection('managemysql')->statement("DROP USER $dbname@localhost");
        DB::connection('managemysql')->statement("DROP DATABASE $dbname");
        DB::connection('managemysql')->statement("FLUSH PRIVILEGES");
    }

    public static function install_wordpress ($url, $title, $username, $password, $email)
    {
        $data = array(
            'weblog_title' => $title,
            'user_name' => $username,
            'admin_password' => $password,
            'admin_password2' => $password,
            'admin_email' => $email,
            'blog_public' => 1,
        );
        $response = Requests::post('http://' . $url . '/wp-admin/install.php?step=2', array(), $data);
    }

    public static function get_wordpress($user, $domain)
    {
        $dir = base_path() . '/users/' . $user->email . '/';
        $url = 'http://wordpress.org/latest.zip';
        file_put_contents($dir . basename($url), file_get_contents($url));

        $zip = new ZipArchive;
        $res = $zip->open($dir . basename($url));
        if ($res === true) {
            $zip->extractTo($dir);
            $zip->close();
            rename($dir . 'wordpress', $dir . $domain);
            unlink($dir . 'latest.zip');
            symlink($dir . $domain, base_path() . '/websites/' . $domain);
            return true;
        } else {
            return false;
        }
    }

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

}
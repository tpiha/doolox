<?php

class DooloxController extends BaseController {

	public function dashboard()
    {
        $rsa = new Crypt_RSA();
        $rsa->loadKey(Config::get('doolox.private_key'));
        $user = Sentry::getUser();
        $sites = $user->getSites()->get();
        foreach ($sites as $site) {
            $link = $site->url;
            $link = str_replace('http://', '', $link);
            $link = str_replace('https://', '', $link);
            if (substr($link, -1) == '/') {
                $link = substr($link, 0, -1);
            }
            $site->link = $link;
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
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->passes()) {
            $input = Input::except('_token');
            if (strpos($input['url'], 'http://') !== 0 && strpos($input['url'], 'https://') !== 0) {
                $input['url'] = 'http://' . $input['url'];
            }
            if (substr($input['url'], -1) != '/') {
                $input['url'] .= '/';
            }
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

        Validator::extend('domainav', function($attribute, $value, $parameters)
        {
            $domain = explode('.', $value);
            $subdomain = $domain[0];
            $domain = $domain[1] . '.' . $domain[2];
            $d = Domain::where('url', $domain)->first();
            if (Site::where('domain_id', $d->id)->where('subdomain', $subdomain)->count()) {
                return false;
            }
            else {
                return true;
            }
        });

        $messages = array(
            'domainav' => 'This domain is not available.',
        );

        $rules = array(
            'url' => 'required|domainav',
        );
        $validator = Validator::make(Input::all(), $rules, $messages);
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

            $subdomain = str_replace($domain . '.', '', $url);
            $d = Domain::where('url', $domain)->first();

            $user = Sentry::getUser();
            $site = Site::create(array('user_id' => $user->id, 'name' => $title, 'url' => 'http://' . $url . '/', 'local' => true, 'admin_url' => '', 'domain_id' => $d->id, 'subdomain' => $subdomain));
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

    public function check_domain($domain)
    {
        $da = DooloxController::is_domain_available($domain, Sentry::getUser());
        return Response::json(array('free' => $da[0], 'status' => $da[1]));
    }

    public function check_subdomain($domain)
    {
        if ($domain != '.' . Config::get('doolox.system_domain')) {
            $domain = explode('.', $domain);
            $subdomain = $domain[0];
            $domain = $domain[1] . '.' . $domain[2];
            $d = Domain::where('url', $domain)->first();
            if (Site::where('domain_id', $d->id)->where('subdomain', $subdomain)->count()) {
                return Response::json(array('free' => false, 'status' => 3));
            }
            else {
                return Response::json(array('free' => true, 'status' => 0));
            }
        }
        else {
            return Response::json(array('free' => false, 'status' => 1));
        }
    }

    public static function is_domain_available($domain, $user)
    {
        // $taken = array('blog', 'wiki', 'admin', '');
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
                return array(false, 1);
            }
        }

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

    public static function namecom_is_available($domain)
    {
        return false;
        require_once(base_path() . "/tools/namecom_api.php");
        $api = new NameComApi();
        $api->login(Config::get('doolox.namecom_user'), Config::get('doolox.namecom_token'));
        $response = $api->check_domain($domain);
        return $response;
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

    public function wpcipher_connect($id, $username) {
        $site = Site::find($id);
        $rsa = new Crypt_RSA();

        if ($site->private_key) {
            $privatekey = $site->private_key;
            $publickey = $site->public_key;
        }
        else {
            extract($rsa->createKey());
            $site->private_key = $privatekey;
            $site->public_key = $publickey;
            $site->save();
        }

        $rsa->loadKey(Config::get('doolox.private_key'));

        $data = array(
            'id' => $site->id,
            'action' => 'connect',
            'username' => $username,
            'public_key' => $publickey,
            'rand' => str_random(32),
            'url' => Config::get('app.url')
        );

        $data = json_encode($data);
        $data = base64_encode($data);
        $data = $rsa->encrypt($data);
        $data = base64_encode($data);
        $data = urlencode($data);

        return Response::json(array('cipher' => $data));
    }

    public function wpcipher_login($id) {
        $site = Site::find($id);

        $rsa = new Crypt_RSA();
        $rsa->loadKey($site->private_key);

        $data = array(
            'id' => (string) $site->id,
            'action' => 'login',
            'rand' => str_random(32),
        );

        $data = json_encode($data);
        $data = base64_encode($data);
        $data = $rsa->encrypt($data);
        $data = base64_encode($data);
        $data = urlencode($data);

        return Response::json(array('cipher' => $data));
    }

    public function connected() {
        $site_id = Input::get('id');
        $site = Site::find($site_id);
        $site->connected = true;
        $site->save();
    }

    public function site_move($id)
    {
        $site = Site::find($id);
        if ($site->local) {
            $domains = array();
            $_domains = Sentry::getUser()->getDomains()->get();
            $selected = 0;
            $selected_url = '';
            foreach ($_domains as $domain) {
                $domains["$domain->url"] = $domain->url;
                $selected_url = $domain->url;
            }
            return View::make('site_move')->with(array('domains' => $domains, 'selected_url' => $selected_url, 'site' => $site));
        }
        else {
            Session::flash('error', 'Remote websites can\'t be migrated.');
            return Redirect::route('doolox.dashboard');
        }
    }

    public function site_move_post($id)
    {
        $domains = Sentry::getUser()->getDomains()->get();
        $site = Site::find($id);
        Validator::extend('domainav', function($attribute, $value, $parameters)
        {
            $domain = explode('.', $value);
            $subdomain = $domain[0];
            $domain = $domain[1] . '.' . $domain[2];

            $d = Domain::where('url', $domain)->first();
            if (Site::where('domain_id', $d->id)->where('subdomain', $subdomain)->count()) {
                return false;
            }
            else if (!strlen($subdomain)) {
                return false;
            }
            else {
                return true;
            }
        });

        $messages = array(
            'domainav' => 'This domain is not available.',
        );

        $rules = array(
            'url' => 'required|domainav',
        );

        $validator = Validator::make(Input::all(), $rules, $messages);

        if ($validator->passes()) {
            $site->change_domain(Input::get('url'));
            Session::flash('success', 'Website successfully moved.');
            return Redirect::route('doolox.dashboard');
        }

        $_domains = array();
        foreach ($domains as $domain) {
            $_domains["$domain->url"] = $domain->url;
            $selected_url = $domain->url;
        }

        Input::flash();

        return View::make('site_move')->withErrors($validator)->with(array('domains' => $_domains, 'selected_url' => $selected_url, 'site' => $site));
    }

    public static function folder_size($dir){
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

    public function upgrade()
    {
        return View::make('upgrade');
    }

}
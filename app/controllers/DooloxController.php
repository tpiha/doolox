<?php

class DooloxController extends BaseController {

    /**
     * Doolox dashboard
     *
     * @return View object
     */
	public function dashboard()
    {
fdsaf
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

    /**
     * Edit Doolox website
     *
     * @param integer $id - Site object id
     * @return View object
     */
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

    /**
     * Add new remote Doolox website
     *
     * @return View object
     */
    public function site_new()
    {
        Validator::extend('wpurl', function($attribute, $value, $parameters)
        {
            if (strpos($value, 'http://') !== 0 && strpos($value, 'https://') !== 0) {
                $value = 'http://' . $value;
            }
            if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/", $value)) {
                return false;
            }
            else {
                return true;
            }
        });

        $messages = array(
            'wpurl' => 'URL is not valid. Allowed characters are lowercase alphanumeric, dash, dot and slash. URL can start with http:// or https://.',
        );

        $rules = array(
            'name' => 'required',
            'url' => 'required|wpurl',
            'username' => 'required_if:doolox_node,true',
            'password' => 'required_if:doolox_node,true',
        );

        $validator = Validator::make(Input::all(), $rules, $messages);

        if ($validator->passes()) {
            $input = Input::except(array('_token', 'username', 'password', 'doolox_node'));
            if (strpos($input['url'], 'http://') !== 0 && strpos($input['url'], 'https://') !== 0) {
                $input['url'] = 'http://' . $input['url'];
            }
            if (substr($input['url'], -1) != '/') {
                $input['url'] .= '/';
            }
            if (strlen($input['admin_url']) && $input['admin_url'][0] == '/') {
                 $input['admin_url'] = substr($input['admin_url'], 1);
            }
            $site = Site::create($input);
            $user = Sentry::getUser();
            $user->getSites()->attach($site);

            if (Input::get('doolox_node')) {
                $install = Doolox::install_doolox_node($input['url'], Input::get('username'), Input::get('password'));
                if ($install) {
                    Session::flash('success', 'New website successfully added with successfull Doolox Node installation.');
                }
                else {
                    Session::flash('success', 'New website successfully added with unsuccessfull Doolox Node installation, please install it manually.');
                }
            }
            else {
                Session::flash('success', 'New website successfully added.');
            }

            return Redirect::route('doolox.dashboard');
        }

        Input::flash();

        return View::make('site_new')->withErrors($validator);
    }

    /**
     * Delete Doolox website
     *
     * @param integer $id - Site object id
     * @return View object
     */
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

    /**
     * Remove user from Doolox website owners
     *
     * @param integer $id - Site object id
     * @param integer $user_id - User object id
     * @return View object
     */
    public function site_rmuser($id, $user_id)
    {
        $site = Site::find($id);
        $site->getUsers()->detach($user_id);
        $site->save();
        Session::flash('success', 'User successfully removed from the website.');
        return Redirect::route('doolox.dashboard');
    }

    /**
     * Add user to Doolox website owners
     *
     * @param integer $id - Site object id
     * @return View object
     */
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

    /**
     * Install new website on Doolox
     *
     * @return View object
     */
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

    /**
     * Install new website on Doolox (post request)
     *
     * @return View object
     */
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

        Validator::extend('wpurl', function($attribute, $value, $parameters)
        {
            if ($value == strtolower($value)) {
                return true;
            }
            else {
                return false;
            }
        });

        $messages = array(
            'domainav' => 'This domain is not available.',
            'wpurl' => 'URL is not valid. Allowed characters are lowercase alphanumeric, dash, and dot.',
        );

        $rules = array(
            'url' => 'required|domainav|wpurl',
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

    /**
     * Install new website on Doolox (step 2)
     *
     * @return View object
     */
    public function site_install_step2()
    {
        $domain = Input::get('domain');
        $url = Input::get('url');
        $url = str_replace('http://', '', $url);
        $url = str_replace('https://', '', $url);

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

            $subdomain = str_replace('.' . $domain, '', $url);
            $d = Domain::where('url', $domain)->first();

            $user = Sentry::getUser();
            $site = Site::create(array('user_id' => $user->id, 'name' => $title, 'url' => 'http://' . $url . '/', 'local' => true, 'admin_url' => '', 'domain_id' => $d->id, 'subdomain' => $subdomain));
            $user->getSites()->attach($site);

            Doolox::get_wordpress(Sentry::getUser(), $url);
            $dbname = 'doolox' . $user->id . '_db' . $site->id;
            $dbpass = str_random(32);
            Doolox::create_database($dbname, $dbname, $dbpass);
            Doolox::create_wp_config($user, $url, $dbname, $dbpass);
            try {
                Doolox::install_wordpress($url, $title, $username, $password, $email);
            }
            catch (Exception $e) {}
            $install = Doolox::install_doolox_node('http://' . $url . '/', $username, $password);
            if ($install) {
                Session::flash('success', 'New website successfully installed with successfull Doolox Node installation.');
            }
            else {
                Session::flash('success', 'New website successfully installed with unsuccessfull Doolox Node installation, please install it manually.');
            }
            return Redirect::route('doolox.dashboard');
        }

        Input::flash();

        return View::make('site_install_step2')->with(array('domain' => $domain, 'url' => $url))->withErrors($validator);
    }

    /**
     * Move Doolox website
     *
     * @param integer $id - Site object id
     * @return View or Redirect object
     */
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

    /**
     * Move Doolox website (post request)
     *
     * @param integer $id - Site object id
     * @return View object
     */
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

    /**
     * Check subdomain on Doolox (new installation purposes)
     *
     * @param string $domain - full domain (with subdomain)
     * @return JSON object
     */
    public function check_subdomain($domain)
    {
        // $taken = array('blog', 'wiki', 'admin', '');
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

    /**
     * Check domain on Doolox (new domain purposes)
     *
     * @param string $domain - full domain (with subdomain)
     * @return JSON object
     */
    public function check_domain($domain)
    {
        $da = Doolox::is_domain_available($domain);
        return Response::json(array('free' => $da[0], 'status' => $da[1]));
    }

    /**
     * Get data for connecting to Doolox Node
     *
     * @param integer $id - Site object id
     * @param string $username - WordPress website username
     * @return JSON object
     */
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

    /**
     * Get data for sign in to Doolox Node
     *
     * @param integer $id - Site object id
     * @return JSON object
     */
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

    /**
     * Doolox website connected handler (called from Doolox Node)
     *
     * @return void
     */
    public function connected() {
        $site_id = Input::get('id');
        $site = Site::find($site_id);
        $site->connected = true;
        $site->save();
    }

    /**
     * Upgrade Doolox plan on SaaS version
     *
     * @return View object
     */
    public function upgrade()
    {
        return View::make('upgrade');
    }

    /**
     * Doolox plan paid handler (called by FastSpring)
     *
     * @return void
     */
    public function paid_plan() {
        $privatekey = Config::get('doolox.fskey');
        $secdata = $_REQUEST['security_data'];
        $sechash = $_REQUEST['security_hash'];

        if (md5($secdata . $privatekey) == $sechash) {
            $product = Input::get('SubscriptionPath');
            $user_id = (int) Input::get('SubscriptionReferrer');

            if ($product == '/pro1month') {
                $group = Sentry::findGroupByName('Doolox Pro');
            }
            else if ($product == '/business1month') {
                $group = Sentry::findGroupByName('Doolox Business');
            }
            else {
                $group = Sentry::findGroupByName('Doolox Unlimited');
            }
            $user = Sentry::findUserById($user_id);
            $user->addGroup($group);

            Log::info('FastSpring - user activated: ' . $user->email);
        }
    }

    /**
     * Doolox domain paid handler (called by FastSpring)
     *
     * @return void
     */
    public function paid_domain() {
        require_once(base_path() . "/tools/namecom_api.php");

        $privatekey = Config::get('doolox.fskey_domain');
        $secdata = $_REQUEST['security_data'];
        $sechash = $_REQUEST['security_hash'];

        if (md5($secdata . $privatekey) == $sechash) {
            $domain = Input::get('SubscriptionReferrer');
            $do = Domain::where('url', $domain)->first();

            $api = new NameComApi();
            $api->baseUrl(Config::get('doolox.namecom_url'));
            $api->username(Config::get('doolox.namecom_user'));
            $api->apiToken(Config::get('doolox.namecom_token'));

            $nameservers = array('ns1.name.com', 'ns2.name.com', 'ns3.name.com', 'ns4.name.com');
            $contacts = array(array('type' => array('registrant', 'administrative', 'technical', 'billing'),
                'first_name' => 'Tihomir',
                'last_name' => 'Piha',
                'organization' => 'Click the page Ltd.',
                'address_1' => 'Marice Baric 3',
                'address_2' => '',
                'city' => 'Zagreb',
                'state' => 'Grad Zagreb',
                'zip' => '10000',
                'country' => 'HR',
                'phone' => '+385955388411',
                'fax' => '+385955388411',
                'email' => 'tpiha@naklikaj.com',
            ));

            $response = $api->create_domain($domain, 1, $nameservers, $contacts);
            $code = intval($response->result->code);

            if ($code == 100) {
                $response1 = $api->create_dns_record($domain, '*', 'A', '176.9.133.107', 300);
                $response2 = $api->create_dns_record($domain, 'mail', 'MX', 'mail.doolox.com', 300, 10);

                $do->activated = true;
                $do->save();

                Log::info('FastSpring - domain activated: ' . $domain . ' ' . $response->result->code . ' ' . $response1->result->code . ' ' . $response2->result->code);
            }
            else {
                Log::info('FastSpring - domain activation failed: ' . $domain . ' ' . $response->result->code);
            }
        }
    }

    /**
     * Install self-hosted Doolox app (ckeckup step)
     *
     * @return View object
     */
    public function install()
    {
        $conds = array();

        $conds['storage'] = is_writable(base_path() . '/app/storage/');
        $conds['database'] = is_writable(base_path() . '/app/config/database.php');
        $conds['doolox'] = is_writable(base_path() . '/app/config/doolox.php');
        $conds['app'] = is_writable(base_path() . '/app/config/app.php');
        $conds['mcrypt'] = extension_loaded('mcrypt');
        $conds['curl'] = extension_loaded('curl');
        $conds['sqlite'] = extension_loaded('pdo_sqlite');
        $conds['mysql'] = extension_loaded('pdo_mysql');
        $conds['pgsql'] = extension_loaded('pdo_pgsql');

        if ($conds['storage'] && $conds['database'] && $conds['doolox'] && $conds['app'] && $conds['mcrypt'] && $conds['curl']) {
            return Redirect::route('doolox.install2');
        }

        return View::make('install')->with('conds', $conds);
    }

    /**
     * Install self-hosted Doolox app (database step)
     *
     * @return View object
     */
    public function install2()
    {
        $app = base_path() . '/app/config/app.php';
        $doolox = base_path() . '/app/config/doolox.php';
        $database = base_path() . '/app/config/database.php';

        Validator::extend('extension_check', function($attribute, $value, $parameters)
        {
            return extension_loaded('pdo_' . $value);
        });

        $messages = array(
            'extension_check' => 'You need to enable PHP extension for the chosen database type.',
        );

        $rules = array(
            'dbhost' => 'required_if:database,mysql|required_if:database,pgsql',
            'dbname' => 'required_if:database,mysql|required_if:database,pgsql',
            'dbuser' => 'required_if:database,mysql|required_if:database,pgsql',
            'dbpass' => 'required_if:database,mysql|required_if:database,pgsql',
            'database' => 'extension_check',
        );

        $validator = Validator::make(Input::all(), $rules, $messages);

        if ($validator->passes()) {
            $url = str_replace('/install2', '', Request::url());
            $key = str_random(32);
            $app_sample = base_path() . '/app/config/app.sample.php';

            Doolox::replace_in_file($app_sample, $app, '__DXURL__', $url);
            Doolox::replace_in_file($app, $app, '__DXKEY__', $key);

            $domain = Config::get('doolox.system_domain');
            $rsa = new Crypt_RSA();
            extract($rsa->createKey());
            $doolox_sample = base_path() . '/app/config/doolox.sample.php';

            Doolox::replace_in_file($doolox_sample, $doolox, '__DXDOMAIN__', $domain);
            Doolox::replace_in_file($doolox, $doolox, '__DXPRIVATE_KEY__', $privatekey);
            Doolox::replace_in_file($doolox, $doolox, '__DXPUBLIC_KEY__', $publickey);

            $database_sample = base_path() . '/app/config/database.sample.php';

            $dbtype = Input::get('database');
            Doolox::replace_in_file($database_sample, $database, '__DXDATABASE__', $dbtype);

            if ($dbtype != 'sqlite') {
                $dbhost = Input::get('dbhost');
                $dbname = Input::get('dbname');
                $dbuser = Input::get('dbuser');
                $dbpass = Input::get('dbpass');

                Doolox::replace_in_file($database, $database, '__DXDBHOST__', $dbhost);
                Doolox::replace_in_file($database, $database, '__DXDBNAME__', $dbname);
                Doolox::replace_in_file($database, $database, '__DXDBUSER__', $dbuser);
                Doolox::replace_in_file($database, $database, '__DXDBPASS__', $dbpass);

                Artisan::call('migrate:install', array());
                Artisan::call('migrate', array());
                Artisan::call('db:seed', array());
            }

            unlink(base_path() . '/app/storage/install');
            Session::flash('success', 'You have successfully installed Doolox. You can now <a href="' . route('user.login') . '">login</a> with these credentials: admin@admin.com / admin');
        }

        return View::make('install2')->withErrors($validator);
    }

}
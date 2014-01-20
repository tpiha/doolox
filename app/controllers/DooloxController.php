<?php

class DooloxController extends BaseController {

	public function dashboard()
    {
        $user = Sentry::getUser();
        $wpsites = $user->getWPSites()->get();
        // $wpsites = array();
        foreach ($wpsites as $wpsite) {
            $wpsite->password = self::rc4($user->key, $wpsite->password);
            $wpsite->username = self::rc4($user->key, $wpsite->username);
        }
		return View::make('dashboard')->with('wpsites', $wpsites);
	}

    public function wpsite($id)
    {
        $validator = null;
        $wpsite = WPSite::findOrFail((int) $id);

        if (Request::has('name')) {
            $rules = array(
                'name' => 'required',
                'url' => 'required',
            );
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->passes()) {
                $wpsite->fill(Input::except('_token'));
                $wpsite->save();
                Session::flash('success', 'Website successfully updated.');
                return Redirect::route('doolox.dashboard');
            }
        }

        return View::make('wpsite')->with('wpsite', $wpsite)->withErrors($validator);
    }

    public function wpsite_new()
    {
        $rules = array(
            'name' => 'required',
            'url' => 'required',
            'username' => 'required',
            'password' => 'required',
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->passes()) {
            $wpsite = WPSite::create(Input::except('_token'));
            $user = Sentry::getUser();
            $user->getWPSites()->attach($wpsite);
            Session::flash('success', 'New website successfully added.');
            return Redirect::route('doolox.dashboard');
        }

        return View::make('wpsite_new')->withErrors($validator);
    }

    public function wpsite_delete($id)
    {
        $wpsite = WPSite::findOrFail((int) $id);
        $wpusersites = WPUserSite::where('wpsite_id', (int) $id)->get();
        foreach ($wpusersites as $wpusersite) {
            // die(var_dump($wpusersite->user_id));
            $wpusersite->delete();
        }
        $wpsite->delete();
        Session::flash('success', 'Website successfully deleted.');
        return Redirect::route('doolox.dashboard');
    }

    public function wpsite_rmuser($id, $user_id)
    {
        $wpsite = WPSite::find($id);
        $wpsite->getUsers()->detach($user_id);
        $wpsite->save();
        Session::flash('success', 'User successfully removed from the website.');
        return Redirect::route('doolox.dashboard');
    }

    public function wpsite_adduser($id)
    {
        if (Input::get('email')) {
            $user = User::where('email', Input::get('email'))->first();
            if ($user) {
                $user->getWPSites()->attach((int) $id);
                $user->save();
                Session::flash('success', 'User successfully added to the website.');
                return Redirect::route('doolox.dashboard');
            }
            else {
                Session::flash('error', 'There is no user with this email.');
                return Redirect::route('doolox.wpsite', array('id' => $id));
            }
        }
        else {
            Session::flash('error', 'Email field is required.');
            return Redirect::route('doolox.wpsite', array('id' => $id));
        }
    }

    public function wpsite_install()
    {
        
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
        $taken = array('blog', 'wiki', 'admin', '');
        $domain = explode('.', $domain);
        try {
            $subdomain = $domain[2];
            $tld = $subdomain;
            $subdomain = $domain[0];
        }
        catch {
            $subdomain = '';
            try {
                $tld = $domain[1];
            }
            catch {
                return array(false, 1);
            }
        }
        if (DooloxController::is_valid_host($domain) && $subdomain == Str::slug($subdomain)) {
            // system domain
            if ($domain == Config::get('doolox.system_domain')) {
                
            }
            // not system, but in database
            else if () {
            }
            // not system, not in database, com, net, org
            else if (in_array($tld, array('com', 'net', 'org'))) {
                if (self::namecom_is_available($domain)) {
                    return array(true, 0);
                }
                else {
                    return array(false, 2);
                }
            }
            // other top level domains
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
            return array(false, 1);
        }
    }

    public static function namecom_is_available() {
        return true;
    }

    public static function is_valid_host() {
        return true;
    }

}
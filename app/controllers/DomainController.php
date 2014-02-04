<?php

class DomainController extends BaseController {

	public function domain_new()
    {
        Validator::extend('domainvalid', function($attribute, $value, $parameters)
        {
            $domain = explode('.', $value);
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
                    return false;
                }
            }
            if ($tld == Str::slug($tld) && $domain == Str::slug($domain) && $subdomain == Str::slug($subdomain)) {
                return true;
            }
            else {
                return false;
            }
        });

        Validator::extend('domainavailable', function($attribute, $value, $parameters)
        {
            $owner = (bool) Input::get('owner', 0);
            $av = DooloxController::is_domain_available($value, Sentry::getUser());
            if ($owner) {
                return true;
            }
            
            if ((int) $av[1] == 2) {
                return false;
            }
            else {
                return true;
            }
        });

        Validator::extend('domainused', function($attribute, $value, $parameters)
        {
            $av = DooloxController::is_domain_available($value, Sentry::getUser());            
            if ((int) $av[1] == 3) {
                return false;
            }
            else {
                return true;
            }
        });

        $messages = array(
            'domainvalid' => '<script type="text/javascript">$(document).ready(function() { $("#domain-invalid").fadeIn();  });</script>',
            'domainavailable' => '<script type="text/javascript">$(document).ready(function() { $("#domain-taken").fadeIn(); $("#owner-parent").fadeIn();  });</script>',
            'domainused' => '<script type="text/javascript">$(document).ready(function() { $("#domain-doolox").fadeIn();  });</script>',
        );

        $rules = array(
            'url' => 'required|domainvalid|domainavailable|domainused',
        );

        $validator = Validator::make(Input::all(), $rules, $messages);

        if ($validator->passes()) {
            $type = Input::get('type');

            $domain = explode('.', Input::get('url'));
            try {
                $subdomain = $domain[2];
                $tld = $subdomain;
                $subdomain = $domain[0];
                $domain = $domain[1];
            }
            catch (Exception $e) {
                $subdomain = '';
                $tld = $domain[1];
                $domain = $domain[0];
            }
            $domain = join(array($domain, $tld), '.');

            $domain = Domain::create(array('user_id' => Sentry::getUser()->id, 'url' => $domain));
            if ($type == 1) {
                Session::flash('success', 'New Doolox domain successfully added.');
                return Redirect::route('domain.index');
            }
            else {
                // return Redirect::route('domain.domain_payment', array('id' => $domain->id));
                return Redirect::to('https://sites.fastspring.com/doolox/instant/dooloxdomain?referrer=' . $domain->url);
            }
        }

        Input::flash();

        return View::make('domain_new')->withErrors($validator);
    }

    public function domain_delete($id)
    {
        $user = Sentry::getUser();
        $domain = Domain::find(intval($id));
        if ($domain->user_id == $user->id && $domain->url != Config::get('doolox.system_domain')) {
            $domain->delete();
            Session::flash('success', 'Doolox domain successfully deleted.');
        }
        else {
            Session::flash('error', 'You are not the owner of this domain.');
        }
        return Redirect::route('domain.index');
    }

}
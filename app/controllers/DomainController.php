<?php

class DomainController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('check-plan');
    }

	public function domain_new()
    {
        Validator::extend('domaindots', function($attribute, $value, $parameters)
        {
            $domain = explode('.', $value);
            if (count($domain) > 2 || count($domain) < 2) {
                return false;
            }
            else {
                return true;
            }
        });

        Validator::extend('domainan', function($attribute, $value, $parameters)
        {
            $domain = explode('.', $value);
            if(count($domain) == 1 || !preg_match('/^[a-z\-\d]+$/', $domain[0]) || !preg_match('/^[a-z\d]+$/', $domain[1])) {
                return false;
            }
            else {
                return true;
            }
        });

        Validator::extend('domainav', function($attribute, $value, $parameters)
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
            'domaindots' => '<script type="text/javascript">$(document).ready(function() { $("#domain-invalid").fadeIn();  });</script>',
            'domainan' => '<script type="text/javascript">$(document).ready(function() { $("#domain-invalid").fadeIn();  });</script>',
            'domainav' => '<script type="text/javascript">$(document).ready(function() { $("#domain-taken").fadeIn(); $("#owner-parent").fadeIn();  });</script>',
            'domainused' => '<script type="text/javascript">$(document).ready(function() { $("#domain-doolox").fadeIn();  });</script>',
        );

        $rules = array(
            'url' => 'required|domaindots|domainan|domainused|domainav',
        );

        $validator = Validator::make(Input::all(), $rules, $messages);

        if ($validator->passes()) {
            $type = Input::get('type');
            $domain = Domain::create(array('user_id' => Sentry::getUser()->id, 'url' => Input::get('url')));
            if ($type == 1) {
                Session::flash('success', 'New Doolox domain successfully added.');
                return Redirect::route('domain.index');
            }
            else {
                // return Redirect::route('domain.domain_payment', array('id' => $domain->id));
                return Redirect::to('https://sites.fastspring.com/doolox/instant/dooloxdomain');
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
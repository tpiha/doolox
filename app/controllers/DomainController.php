<?php

class DomainController extends BaseController {

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
            dd($av);
            return ($owner || $av[0]);
        });

        $messages = array(
            'domaindots' => 'Please search without the subdomain part (www).',
            'domainan' => 'Domains can only contain lowercase alphanumeric characters and dash.',
            'domainav' => 'This domain is not available.',
        );

        $rules = array(
            'url' => 'required|domaindots|domainan|domainav',
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
                return Redirect::route('domain.domain_payment', array('id' => $domain->id));
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
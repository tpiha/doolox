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
            return DooloxController::is_domain_available($value, Sentry::getUser());
        });

        $messages = array(
            'domaindots' => 'Please search without the subdomain part (www).',
            'domainan' => 'Domains can only contain lowercase alphanumeric characters and dash.',
            'domainav' => 'This domain is not available.',
        );

        $rules = array(
            'domain' => 'required|domaindots|domainan|domainav',
        );

        $validator = Validator::make(Input::all(), $rules, $messages);

        if ($validator->passes()) {
            Domain::create(array('user_id' => Sentry::getUser()->id, 'url' => Input::get('domain')));
            Session::flash('success', 'New Doolox domain successfully added.');
            return Redirect::route('domain.index');
        }

        Input::flash();

        return View::make('domain_new')->withErrors($validator);
    }

    public function domain_delete($id)
    {
        $domain = Domain::find($id)->first();
        $domain->delete();
        Session::flash('success', 'Doolox domain successfully deleted.');
        return Redirect::route('domain.index');
    }

}
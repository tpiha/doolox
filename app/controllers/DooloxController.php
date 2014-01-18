<?php

class DooloxController extends BaseController {

	public function dashboard()
	{
        $user = Auth::user();
        $wpsites = $user->getWPSites()->get();
		return View::make('dashboard')->with('wpsites', $wpsites);
	}

    public function wpsite($id)
    {
        $wpsite = WPSite::findOrFail((int) $id);
        return View::make('wpsite')->with('wpsite', $wpsite);
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
            $user = Auth::user();
            $user->getWPSites()->attach($wpsite);
            Session::flash('success', 'New website successfully added.');
            return Redirect::route('doolox.dashboard');
        }

        return View::make('wpsite_new')->withErrors($validator);
    }

}
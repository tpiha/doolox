<?php

class DooloxController extends BaseController {

	public function dashboard(){
        $user = Auth::user();
        $wpsites = $user->getWPSites()->get();
		return View::make('dashboard')->with('wpsites', $wpsites);
	}

    public function wpsite($id){
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

    public function wpsite_new() {
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

    public function wpsite_delete($id) {
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

    public function wpsite_rmuser($id, $user_id) {
        $wpsite = WPSite::find($id);
        $wpsite->getUsers()->detach($user_id);
        $wpsite->save();
        Session::flash('success', 'User successfully removed from the website.');
        return Redirect::route('doolox.dashboard');
    }

    public function wpsite_adduser($id) {
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

}
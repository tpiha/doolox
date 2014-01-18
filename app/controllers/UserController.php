<?php

class UserController extends BaseController {

	public function login()
	{
        $email = Input::get('email');
        $password = Input::get('password');

        $rules = array(
            'email' => 'required|email',
            'password' => 'required',
        );

        $validator = Validator::make(Input::all(), $rules);
        if ($validator->passes()) {
            if (Auth::attempt(array('email' => $email, 'password' => $password), true)) {
                return Redirect::intended();
            }
            else {
                Session::flash('error', 'Login failed. Check yout email and password.');
            }
        }

        return View::make('login')->withErrors($validator);
	}

}
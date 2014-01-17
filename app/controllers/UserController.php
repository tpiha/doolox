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
        if ($validator->passes() && Auth::attempt(array('email' => $email, 'password' => $password), true)) {
            return Redirect::intended();
        }

        return View::make('login')->withErrors($validator);
	}

}
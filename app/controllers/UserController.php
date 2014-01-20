<?php

class UserController extends BaseController {

	public function login()
    {
        $email = Input::get('email');
        $password = Input::get('password');
        $rememberme = Input::get('rememberme');

        $rules = array(
            'email' => 'required|email',
            'password' => 'required',
        );

        $validator = Validator::make(Input::all(), $rules);
        if ($validator->passes()) {
            if ($user = Sentry::authenticate(array('email' => $email, 'password' => $password), $rememberme)) {
                $user = Sentry::getUser();
                $user->key = DooloxController::generate_key();
                $user->save();
                return Redirect::route('doolox.dashboard')->with('key', $user->key);
                return Redirect::route('doolox.dashboard');
            }
            else {
                Session::flash('error', 'Login failed. Check yout email and password.');
            }
        }

        return View::make('login')->withErrors($validator);
	}

    public function account()
    {
        $user = Sentry::getUser();

        $rules = array(
            'email' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->passes()) {
            $user = Sentry::getUser();

            if (Input::get('password1') != Input::get('password2')) {
                Session::flash('error', 'Passwords don\'t match.');
            }
            else {
                if ($user->email != Input::get('email')) {
                    $user->email = Input::get('email');
                    $user->save();
                }

                if (strlen(Input::get('password1'))) {
                    $user->password = Input::get('password1');
                    $user->save();
                }

                Session::flash('success', 'Account successfully updated.');
                return Redirect::route('doolox.dashboard');
            }
        }

        return View::make('account')->with('user', $user)->withErrors($validator);
    }

    public function manage_users()
    {
        $user = Sentry::getUser();
        if ($user->isSuperUser()) {
            $users = User::all();
        }
        else {
            $users = User::where('parent_id', $user->id)->get();
        }
        return View::make('manage_users')->with('users', $users);
    }

    public function user_new()
    {
        $user = Sentry::getUser();

        $rules = array(
            'email' => 'required|email|unique:users',
            'password1' => 'required|same:password2',
            'password2' => 'required|same:password1',
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->passes()) {
            Sentry::createUser(array('email' => Input::get('email'), 'password' => Input::get('password1'), 'activated' => 1, 'parent_id' => $user->id));
            Session::flash('success', 'New user successfully added.');
            return Redirect::route('user.manage_users');
        }

        return View::make('user_new')->withErrors($validator);
    }

    public function user_delete($id)
    {
        $user = User::findOrFail((int) $id);
        $auth_user = Sentry::getUser();

        if ($auth_user->isSuperUser() || $auth_user->id == $user->parent_id) {
            $wpusersites = WPUserSite::where('user_id', (int) $id)->get();
            foreach ($wpusersites as $wpusersite) {
                $wpusersite->delete();
            }
            $user->delete();
            Session::flash('success', 'User successfully deleted.');
            return Redirect::route('user.manage_users');
        }
        else {
            Session::flash('error', 'You don\'t have permissions to manage this user.');
            return Redirect::route('user.manage_users');
        }
    }

    public function user_update($id)
    {
        $user = User::findOrFail((int) $id);
        $auth_user = Sentry::getUser();

        if ($auth_user->isSuperUser() || $auth_user->id == $user->parent_id) {
            $rules = array(
                'email' => 'required|email',
                'password1' => 'same:password2',
                'password2' => 'same:password1',
            );

            $validator = Validator::make(Input::all(), $rules);

            if ($validator->passes()) {
                $user->fill(array('email' => Input::get('email'), 'password' => Input::get('password1')));
                $user->save();
                Session::flash('success', 'User successfully updated.');
                return Redirect::route('user.manage_users');
            }

            return View::make('user_update')->with('user', $user)->withErrors($validator);
        }
        else {
            Session::flash('error', 'You don\'t have permissions to manage this user.');
            return Redirect::route('user.manage_users');
        }
    }

}
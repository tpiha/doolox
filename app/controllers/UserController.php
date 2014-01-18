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

    public function account()
    {
        $user = Auth::user();

        $rules = array(
            'email' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->passes()) {
            $user = Auth::user();

            if (Input::get('password1') != Input::get('password2')) {
                Session::flash('error', 'Passwords don\'t match.');
            }
            else {
                if ($user->email != Input::get('email')) {
                    $user->email = Input::get('email');
                    $user->save();
                }

                if (strlen(Input::get('password1'))) {
                    $user->password = Hash::make(Input::get('password1'));
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
        $user = Auth::user();
        if ($user->superuser) {
            $users = User::all();
        }
        else {
            $users = User::where('parent_id', $user->id)->get();
        }
        return View::make('manage_users')->with('users', $users);
    }

    public function user_new()
    {
        $user = Auth::user();

        $rules = array(
            'email' => 'required|email|unique:users',
            'password1' => 'required|same:password2',
            'password2' => 'required|same:password1',
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->passes()) {
            User::create(array('email' => Input::get('email'), 'password' => Hash::make(Input::get('password1')), 'superuser' => 0, 'parent_id' => $user->id));
            Session::flash('success', 'New user successfully added.');
            return Redirect::route('user.manage_users');
        }

        return View::make('user_new')->withErrors($validator);
    }

    public function user_delete($id)
    {
        $user = User::findOrFail((int) $id);
        $auth_user = Auth::user();

        if ($auth_user->superuser || $auth_user->id == $user->parent_id) {
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
        $auth_user = Auth::user();

        if ($auth_user->superuser || $auth_user->id == $user->parent_id) {
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
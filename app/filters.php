<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	//
});


App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest()) return Redirect::guest('login');
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

Route::filter('owner', function()
{
    $user = Sentry::getUser();
    $id = Route::input('id');
    if (!$user->getWPSites()->get()->contains((int) $id)) {
        return Redirect::route('doolox.dashboard');
    }
});

Route::filter('user-management', function()
{
    $user = Sentry::getUser();
    $allow_user_management = Config::get('doolox.allow_user_management');
    $redirect = false;

    if (!$user) {
        $redirect = true;
    }
    else if (!$allow_user_management && !$user->isSuperUser()) {
        $redirect = true;
    }

    if ($redirect) {
            Session::flash('error', 'You don\'t have permissions to access this link.');
            if (Auth::guest()) {
                return Redirect::route('user.login');
            }
            else {
                return Redirect::route('doolox.dashboard');
            }
        }
});

/*
|--------------------------------------------------------------------------
| Admin auth filter
|--------------------------------------------------------------------------
| You need to give your routes a name before using this filter.
| I assume you are using resource. so the route for the UsersController index method
| will be admin.users.index then the filter will look for permission on users.view
| You can provide your own rule by passing a argument to the filter
|
*/
Route::filter('auth.doolox', function($route, $request, $userRule = null)
{
    if (! Sentry::check())
    {
        Session::put('url.intended', URL::full());
        return Redirect::route('user.login');
    }

    // no special route name passed, use the current name route
    if ( is_null($userRule) )
    {
        list($prefix, $module, $rule) = explode('.', Route::currentRouteName());
        switch ($rule)
        {
            case 'index':
            case 'show':
                $userRule = $module.'.view';
                break;
            case 'create':
            case 'store':
                $userRule = $module.'.create';
                break;
            case 'edit':
            case 'update':
                $userRule = $module.'.update';
                break;
            case 'destroy':
                $userRule = $module.'.delete';
                break;
            default:
                $userRule = Route::currentRouteName();
                break;
        }
    }
    // // no access to the request page and request page not the root admin page
    // if ( ! Sentry::hasAccess($userRule) and $userRule !== 'cpanel.view' )
    // {
    //     return Redirect::route('cpanel.home')
    //         ->with('error', Lang::get('cpanel::permissions.access_denied'));
    // }
    // // no access to the request page and request page is the root admin page
    // else if( ! Sentry::hasAccess($userRule) and $userRule === 'cpanel.view' )
    // {
    //     //can't see the admin home page go back to home site page
    //     return Redirect::to('/')
    //         ->with('error', Lang::get('cpanel::permissions.access_denied'));
    // }

});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});
<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', array(
    'as' => 'doolox.dashboard',
    'before' => 'auth',
    'uses' => 'DooloxController@dashboard',
));

Route::post('login', array(
    'as' => 'user.login',
    'uses' => 'UserController@login',
));

Route::get('login', array(
    'as' => 'user.login',
    function() {
        return View::make('login');
    }
));

Route::get('logout', array(
    'as' => 'user.logout',
    function () {
        Auth::logout();
        return Redirect::route('user.login');
    }
));

Route::get('wplogin', array(
    'as' => 'doolox.wplogin',
    'before' => 'auth',
    'uses' => 'DooloxController@wplogin',
));
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

Route::get('wpsite/{id}', array(
    'as' => 'doolox.wpsite',
    'before' => 'owner',
    'uses' => 'DooloxController@wpsite',
));

Route::post('wpsite/{id}', array(
    'as' => 'doolox.wpsite',
    'before' => 'auth',
    'uses' => 'DooloxController@wpsite',
));

Route::get('wpsite-new', array(
    'as' => 'doolox.wpsite_new',
    'before' => 'auth',
    function () {
        return View::make('wpsite_new');
    }
));

Route::post('wpsite-new', array(
    'as' => 'doolox.wpsite_new',
    'before' => 'auth',
    'uses' => 'DooloxController@wpsite_new',
));

Route::get('wpsite-delete/{id}', array(
    'as' => 'doolox.wpsite_delete',
    'before' => 'auth',
    'uses' => 'DooloxController@wpsite_delete',
));

Route::get('account', array(
    'as' => 'user.account',
    'before' => 'auth',
    function() {
        return View::make('account')->with('user', Auth::user());
    }
));

Route::post('account', array(
    'as' => 'user.account',
    'before' => 'auth',
    'uses' => 'UserController@account',
));
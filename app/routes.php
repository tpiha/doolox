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

Route::group(array('before' => 'check-plan'), function()
{

    Route::get('/', array(
        'as' => 'doolox.dashboard',
        'before' => 'auth.doolox:doolox.view',
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
            Sentry::logout();
            return Redirect::route('user.login');
        }
    ));

    Route::get('site/{id}', array(
        'as' => 'doolox.site',
        'before' => 'owner',
        'uses' => 'DooloxController@site',
    ));

    Route::post('site/{id}', array(
        'as' => 'doolox.site',
        'before' => 'owner',
        'uses' => 'DooloxController@site',
    ));

    Route::get('site-new', array(
        'as' => 'doolox.site_new',
        'before' => 'auth.doolox:doolox.update|limit-remote',
        function () {
            return View::make('site_new');
        }
    ));

    Route::post('site-new', array(
        'as' => 'doolox.site_new',
        'before' => 'auth.doolox:doolox.update',
        'uses' => 'DooloxController@site_new',
    ));

    Route::get('site-delete/{id}', array(
        'as' => 'doolox.site_delete',
        'before' => 'auth.doolox:doolox.delete',
        'uses' => 'DooloxController@site_delete',
    ));

    Route::get('site-move/{id}', array(
        'as' => 'doolox.site_move',
        'before' => 'auth.doolox:doolox.update',
        'uses' => 'DooloxController@site_move',
    ));

    Route::post('site-move-post/{id}', array(
        'as' => 'doolox.site_move_post',
        'before' => 'auth.doolox:doolox.update',
        'uses' => 'DooloxController@site_move_post',
    ));

    Route::get('account', array(
        'as' => 'user.account',
        'before' => 'auth.doolox:doolox.view',
        function() {
            return View::make('account')->with('user', Sentry::getUser());
        }
    ));

    Route::post('account', array(
        'as' => 'user.account',
        'before' => 'auth.doolox:doolox.view',
        'uses' => 'UserController@account',
    ));

    Route::get('site-rmuser/{id}/{user_id}', array(
        'as' => 'doolox.site_rmuser',
        'before' => 'owner',
        'uses' => 'DooloxController@site_rmuser',
    ));

    Route::post('site-adduser/{id}', array(
        'as' => 'doolox.site_adduser',
        'before' => 'owner',
        'uses' => 'DooloxController@site_adduser',
    ));

    Route::get('users', array(
        'as' => 'user.manage_users',
        'before' => 'user-management',
        'uses' => 'UserController@manage_users',
    ));

    Route::get('user-new', array(
        'as' => 'user.user_new',
        'before' => 'user-management',
        function() {
            return View::make('user_new');
        }
    ));

    Route::post('user-new', array(
        'as' => 'user.user_new',
        'before' => 'user-management',
        'uses' => 'UserController@user_new',
    ));

    Route::get('user-delete/{id}', array(
        'as' => 'user.user_delete',
        'before' => 'user-management',
        'uses' => 'UserController@user_delete',
    ));

    Route::get('user-update/{id}', array(
        'as' => 'user.user_update',
        'before' => 'user-management',
        function($id) {
            return View::make('user_update')->with('user', User::findOrFail((int) $id));
        }
    ));

    Route::post('user-update/{id}', array(
        'as' => 'user.user_update',
        'before' => 'user-management',
        'uses' => 'UserController@user_update',
    ));

    Route::get('site-install', array(
        'as' => 'doolox.site_install',
        'before' => 'auth.doolox:doolox.view|limit-local|limit-size',
        'uses' => 'DooloxController@site_install',
    ));

    Route::post('site-install-post', array(
        'as' => 'doolox.site_install_post',
        'before' => 'auth.doolox:doolox.view|limit-local|limit-size',
        'uses' => 'DooloxController@site_install_post',
    ));

    Route::get('site-install-step2', array(
        'as' => 'doolox.site_install_step2',
        'before' => 'auth.doolox:doolox.view|limit-local|limit-size',
        function() {
            return View::make('site_install_step2')->with(array('domain' => Session::get('domain'), 'url' => Session::get('url')));
        }
    ));

    Route::post('site-install-step2', array(
        'as' => 'doolox.site_install_step2',
        'before' => 'auth.doolox:doolox.view|limit-local|limit-size',
        'uses' => 'DooloxController@site_install_step2',
    ));

    Route::get('check-domain/{domain}', array(
        'as' => 'doolox.check_domain',
        'before' => 'auth.doolox:doolox.view',
        'uses' => 'DooloxController@check_domain',
    ));

    Route::get('check-subdomain/{domain}', array(
        'as' => 'doolox.check_subdomain',
        'before' => 'auth.doolox:doolox.view',
        'uses' => 'DooloxController@check_subdomain',
    ));

    Route::get('register', array(
        'as' => 'user.register',
        function() {
            if (Config::get('doolox.registration')) {
                return View::make('user_register');
            }
            else {
                Session::flash('error', 'Registration is disabled, plase contact your Doolox admin.');
                return Redirect::route('user.login');
            }
        }
    ));

    Route::post('register', array(
        'as' => 'user.register',
        'uses' => 'UserController@register',
    ));

    Route::get('activate/{user_id}/{code}', array(
        'as' => 'user.activate',
        'uses' => 'UserController@activate',
    ));

    Route::get('domains', array(
        'as' => 'domain.index',
        'before' => 'auth.doolox:doolox.view',
        function() {
            return View::make('manage_domains')->with('domains', Sentry::getUser()->getDomains()->get());
        }
    ));

    Route::get('domain-new', array(
        'as' => 'domain.domain_new',
        'before' => 'auth.doolox:doolox.view',
        function() {
            return View::make('domain_new');
        }
    ));

    Route::post('domain-new', array(
        'as' => 'domain.domain_new',
        'before' => 'auth.doolox:doolox.view',
        'uses' => 'DomainController@domain_new',
    ));

    Route::get('domain-delete/{id}', array(
        'as' => 'domain.domain_delete',
        'before' => 'auth.doolox:doolox.view',
        'uses' => 'DomainController@domain_delete',
    ));

    Route::get('domain-payment/{id}', array(
        'as' => 'domain.domain_payment',
        'before' => 'auth.doolox:doolox.view',
        function($id) {
            return View::make('domain_payment');
        }
    ));

    Route::post('ipn', array(
        'uses' => 'IpnController@store',
        'as' => 'ipn'
    ));

    Route::get('wpcipher-connect/{id}/{username}', array(
        'as' => 'doolox.wpcipher_connect',
        'before' => 'auth.doolox:doolox.view',
        'uses' => 'DooloxController@wpcipher_connect',
    ));

    Route::get('wpcipher-login/{id}', array(
        'as' => 'doolox.wpcipher_login',
        'before' => 'auth.doolox:doolox.view',
        'uses' => 'DooloxController@wpcipher_login',
    ));

    Route::post('connected', array(
        'as' => 'doolox.connected',
        'uses' => 'DooloxController@connected',
    ));

    Route::get('upgrade', array(
        'as' => 'doolox.upgrade',
        'before' => 'auth.doolox:doolox.view',
        'uses' => 'DooloxController@upgrade',
    ));

});
<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Doolox Domain
    |--------------------------------------------------------------------------
    |
    | Doolox domain without the subdomain part.
    |
    */

    'system_domain' => 'doolox.loc',

	/*
	|--------------------------------------------------------------------------
	| Allow User Management To Regular Users
	|--------------------------------------------------------------------------
	|
	| When you set this to true, all users can manage their sub-users. When set
    | to false, only superusers can manage Doolox users.
	|
	*/

	'allow_user_management' => true,

    /*
    |--------------------------------------------------------------------------
    | Registration For New Users
    |--------------------------------------------------------------------------
    |
    | If set to true new users can register by themselves. If set to false,
    | only existing users (or superuser) can add new users.
    |
    */

    'registration' => true,

    /*
    |--------------------------------------------------------------------------
    | Enable Hosting Features
    |--------------------------------------------------------------------------
    |
    | If set to true, Doolox users can install WordPress websites on the Doolox
    | server, activate domains for them etc. This way Doolox serves as a website
    | builder. Needs an extra setup.
    |
    */

    'hosting' => true,

    'namecom_user' => '',

    'namecom_token' => '',

    'namecom_url' => 'https://api.dev.name.com/api',

);

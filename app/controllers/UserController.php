<?php

class UserController extends BaseController {

	public function login()
	{
		return View::make('login');
	}

}
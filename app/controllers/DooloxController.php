<?php

class DooloxController extends BaseController {

	public function dashboard()
	{
        $user = Auth::user();
        $wpsites = $user->getWPSites()->get();
		return View::make('dashboard')->with('wpsites', $wpsites);
	}

    public function wplogin()
    {
        $encrypted = Crypt::encrypt('moj test');
        $decrypted = Crypt::decrypt($encrypted);
        die($decrypted);
    }

}
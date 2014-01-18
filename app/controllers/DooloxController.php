<?php

class DooloxController extends BaseController {

	public function dashboard()
	{
        $user = Auth::user();
        $wpsites = $user->getWPSites()->get();
		return View::make('dashboard')->with('wpsites', $wpsites);
	}

    public function wpsite($id)
    {
        $wpsite = WPSite::findOrFail((int) $id);
        return View::make('wpsite')->with('wpsite', $wpsite);
    }

}
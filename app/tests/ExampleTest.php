<?php

class ExampleTest extends TestCase {

	/**
	 * A basic functional test example.
	 *
	 * @return void
	 */
	public function testGuestRedirects()
	{
        Route::enableFilters();

		$crawler = $this->call('GET', URL::route('doolox.dashboard'));
		$this->assertRedirectedToRoute('user.login');

        $crawler = $this->call('GET', URL::route('doolox.dashboard_registered'));
        $this->assertRedirectedToRoute('user.login');

        $crawler = $this->call('GET', URL::route('doolox.site_new'));
        $this->assertRedirectedToRoute('user.login');

        $crawler = $this->call('GET', URL::route('doolox.site_delete', array(1)));
        $this->assertRedirectedToRoute('user.login');

        $crawler = $this->call('GET', URL::route('doolox.site_move', array(1)));
        $this->assertRedirectedToRoute('user.login');

        $crawler = $this->call('POST', URL::route('doolox.site_move_post', array(1)));
        $this->assertRedirectedToRoute('user.login');

        $crawler = $this->call('GET', URL::route('user.account'));
        $this->assertRedirectedToRoute('user.login');
	}

}
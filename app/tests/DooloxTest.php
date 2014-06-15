<?php

class DooloxTest extends TestCase {

    protected $useDatabase = true;

	/**
	 * Test guest redirects
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

    /**
     * Test login and logout
     *
     * @return void
     */
    public function testLogin()
    {
        Route::enableFilters();

        $post_data = array(
            'email' => 'admin@admin.com',
            'password' => 'admin',
        );

        $crawler = $this->action('POST', 'user.login', $post_data);
        $this->assertRedirectedToRoute('doolox.dashboard');

        $crawler = $this->action('GET', 'user.logout');
        $this->assertRedirectedToRoute('user.login');

        $crawler = $this->action('GET', 'doolox.dashboard');
        $this->assertRedirectedToRoute('user.login');
    }

}
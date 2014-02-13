<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase {

    protected $useDatabase = true;

	/**
	 * Creates the application.
	 *
	 * @return \Symfony\Component\HttpKernel\HttpKernelInterface
	 */
	public function createApplication()
	{
		$unitTesting = true;

		$testEnvironment = 'testing';

		return require __DIR__.'/../../bootstrap/start.php';
	}

    public function setUp()
    {
        parent::setUp();
        if ($this->useDatabase) {
            $this->setUpDb();
        }
    }

    public function setUpDb()
    {
        Artisan::call('migrate:install');
        Artisan::call('migrate');
        Artisan::call('db:seed');
    }

}

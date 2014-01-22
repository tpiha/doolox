<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		$this->call('UserSeeder');
        $this->call('DomainSeeder');
	}

}

class UserSeeder extends Seeder {

    public function run()
    {
        DB::table('users')->delete();

        Sentry::createUser(array('email' => 'admin@admin.com', 'password' => 'admin', 'md5password' => md5('admin'), 'activated' => 1));
    }

}

class DomainSeeder extends Seeder {

    public function run()
    {
        DB::table('domains')->delete();

        Domain::create(array('user_id' => 1, 'url' => Config::get('doolox.system_domain'), 'system_domain' => true));
    }

}
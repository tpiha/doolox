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
        $this->call('GroupSeeder');
        $this->call('DomainSeeder');
	}

}

class UserSeeder extends Seeder {

    public function run()
    {
        DB::table('users')->delete();

        Sentry::createUser(array('email' => 'admin@admin.com', 'password' => 'admin', 'md5password' => md5('admin'), 'activated' => 1, 'permissions' => array('superuser' => 1) ));
    }

}

class GroupSeeder extends Seeder {

    public function run()
    {
        DB::table('groups')->delete();

        Sentry::createGroup(array('name' => 'Doolox Pro'));
        Sentry::createGroup(array('name' => 'Doolox Business'));
        Sentry::createGroup(array('name' => 'Doolox Unlimited'));
    }

}

class DomainSeeder extends Seeder {

    public function run()
    {
        DB::table('domains')->delete();

        Domain::create(array('user_id' => 1, 'url' => Config::get('doolox.system_domain'), 'system_domain' => true));
    }

}
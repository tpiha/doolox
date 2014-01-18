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
	}

}

class UserSeeder extends Seeder {

    public function run()
    {
        DB::table('users')->delete();

        User::create(array('email' => 'tpiha@naklikaj.com', 'password' => Hash::make('admin'), 'superuser' => true));
    }

}
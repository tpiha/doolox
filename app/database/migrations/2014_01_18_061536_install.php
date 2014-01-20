<?php

use Illuminate\Database\Migrations\Migration;

class Install extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('wpsites', function($table) {
            $table->increments('id');
            $table->string('name');
            $table->string('url');
            $table->string('username');
            $table->string('password');
            $table->string('admin_url')->nullable();
        });

        Schema::create('user_wpsite', function($table) {
            $table->increments('id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('wpsite_id')->unsigned();
            $table->timestamps();
        });

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('user_profiles');
        Schema::drop('wpsites');
        Schema::drop('user_wpsite');
	}

}
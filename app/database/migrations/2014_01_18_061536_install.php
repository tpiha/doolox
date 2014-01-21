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
        Schema::create('sites', function($table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');;
            $table->string('name');
            $table->string('url');
            $table->string('username');
            $table->string('password');
            $table->string('admin_url')->nullable();
            $table->boolean('local');
        });

        Schema::create('site_user', function($table) {
            $table->increments('id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('site_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');;
            $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');;
            $table->timestamps();
        });

        Schema::create('domains', function($table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');;
            $table->string('url');
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
        Schema::drop('site_user');
        Schema::drop('sites');
        Schema::drop('domains');
	}

}
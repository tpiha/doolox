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
        Schema::create('domains', function($table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('url');
            $table->boolean('activated');
            $table->dateTime('activated_at');
            $table->timestamps();
            $table->boolean('system_domain');
            $table->boolean('auto_billing');

            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('sites', function($table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('name');
            $table->string('url');
            $table->string('username');
            $table->string('password');
            $table->string('admin_url')->nullable();
            $table->boolean('local');
            $table->string('subdomain')->nullable();
            $table->integer('domain_id')->unsigned()->nullable();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('domain_id')->references('id')->on('domains');
        });

        Schema::create('site_user', function($table) {
            $table->increments('id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('site_id')->unsigned();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
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
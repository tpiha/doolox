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
            $table->boolean('activated')->default(0);;
            $table->dateTime('activated_at')->default(Carbon::now());
            $table->timestamps();
            $table->boolean('system_domain')->default(0);;
            $table->boolean('auto_billing')->default(0);;

            $table->foreign('user_id')->references('id')->on('users');
            $table->engine = 'InnoDB';
            $table->unique('url');
        });

        Schema::create('sites', function($table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('name');
            $table->string('url');
            $table->string('admin_url')->nullable();
            $table->boolean('local')->default(0);
            $table->string('subdomain')->nullable();
            $table->integer('domain_id')->unsigned()->nullable();
            $table->boolean('connected')->default(0);;
            $table->string('private_key', 1023)->nullable();
            $table->string('public_key', 1023)->nullable();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('domain_id')->references('id')->on('domains');
            $table->engine = 'InnoDB';
        });

        Schema::create('site_user', function($table) {
            $table->increments('id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('site_id')->unsigned();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
            $table->engine = 'InnoDB';
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
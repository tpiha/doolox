<?php

use Illuminate\Database\Migrations\Migration;

class CreateWpsitesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('wpsites', function($table)
        {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('url')->unique();
            $table->string('username');
            $table->string('password');
            $table->string('admin_url')->nullable();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('wpsites');
	}

}

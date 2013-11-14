<?php

use Illuminate\Database\Migrations\Migration;

class EditUsersTable extends Migration{

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users', function($table){
			$table->string('location')->nullable();
			$table->string('url')->nullable();
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('users', function($table){
			$table->drop_column('location');
			$table->drop_column('url');
		});
	}

}
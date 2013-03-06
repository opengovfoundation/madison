<?php

class Create_Users_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function($table){
			$table->engine = "InnoDB";
			$table->increments('id');
			$table->string('email')->unique();
			$table->string('password', 32);
			$table->string('fname');
			$table->string('lname');
			$table->string('phone')->nullable();
			$table->integer('org_id')->unsigned()->nullable();
			$table->string('position')->nullable();
			$table->integer('user_level')->unsigned()->default(3);
			$table->timestamp('last_login')->nullable();
			$table->timestamps();
			
			//Set foreign keys
			$table->foreign('org_id')->references('id')->on('organizations')->on_delete('set null');
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}
<?php

class Create_Bills_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bills', function($table){
			$table->engine = "InnoDB";
			$table->increments('id');
			$table->string('title');
			$table->string('slug')->unique();
			$table->string('shortname')->nullable();
			$table->integer('init_section')->unsigned()->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('bills');
	}

}
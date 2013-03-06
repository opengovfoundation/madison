<?php

class Create_Bill_Meta_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bill_meta', function($table){
			$table->engine = "InnoDB";
			$table->increments('id');
			$table->integer('bill_id')->unsigned();
			$table->string('meta_key');
			$table->string('meta_value')->nullable();
			$table->timestamps();
			
			//Set foreign keys
			$table->foreign('bill_id')->references('id')->on('bills')->on_delete('cascade');
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('bill_meta');
	}

}
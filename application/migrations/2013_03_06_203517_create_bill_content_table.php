<?php

class Create_Bill_Content_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bill_content', function($table){
			$table->engine = "InnoDB";
			$table->increments('id');
			$table->integer('bill_id')->unsigned();
			$table->integer('parent_id')->unsigned();
			$table->integer('child_priority')->unsigned()->default(0);
			$table->text('content');
			$table->timestamps();
			
			//Set foreign keys
			$table->foreign('bill_id')->references('id')->on('bills')->on_delete('cascade');
			$table->foreign('parent_id')->references('id')->on('bill_content')->on_delete('cascade');
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('bill_content');
	}

}
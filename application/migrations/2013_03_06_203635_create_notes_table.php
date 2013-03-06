<?php

class Create_Notes_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('notes', function($table){
			$table->engine = 'InnoDB';
			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->integer('section_id')->unsigned();
			$table->integer('parent_id')->unsigned();
			$table->string('type');
			$table->text('content');
			$table->integer('likes')->unsigned();
			$table->integer('dislikes')->unsigned();
			$table->integer('flags')->unsigned();
			$table->timestamps();
			
			//Set foreign keys
			$table->foreign('user_id')->references('id')->on('users')->on_delete('restrict');
			$table->foreign('section_id')->references('id')->on('bill_content')->on_delete('restrict');
			$table->foreign('parent_id')->references('id')->on('notes')->on_delete('restrict');
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('notes');
	}

}
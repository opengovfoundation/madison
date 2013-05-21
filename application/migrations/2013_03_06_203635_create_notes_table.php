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
			$table->integer('doc_id')->unsigned();
			$table->integer('parent_id')->unsigned()->nullable();
			$table->string('type');
			$table->text('content');
			$table->integer('likes')->unsigned()->default(0);
			$table->integer('dislikes')->unsigned()->default(0);
			$table->integer('flags')->unsigned()->default(0);
			$table->timestamps();
			
			//Set foreign keys
			$table->foreign('user_id')->references('id')->on('users')->on_delete('restrict');
			$table->foreign('section_id')->references('id')->on('doc_contents')->on_delete('restrict');
			$table->foreign('doc_id')->references('id')->on('docs')->on_delete('restrict');
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
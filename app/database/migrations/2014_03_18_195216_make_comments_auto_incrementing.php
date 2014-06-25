<?php

class MakeCommentsAutoIncrementing extends DualMigration {

	public function upMySQL(){
		DB::statement('ALTER TABLE `annotation_comments` CHANGE COLUMN `id` `id` INT(10) unsigned auto_increment primary key');
	}

	public function downMySQL(){
		DB::statement('ALTER TABLE `annotation_comments` CHANGE COLUMN `id` `id` INT(10) unsigned');
	}

	public function upSQLite()
	{
		Schema::create('annotation_comments_temp', function($table) {
			$table->engine = "InnoDB";
			
			$table->increments('id');
			$table->integer('annotation_id')->unsigned();
			$table->integer('user_id')->unsigned();
			$table->string('text');
			$table->timestamps();
			
			$table->foreign('annotation_id')->references('id')->on('annotations')->on_delete('cascade');
			$table->foreign('user_id')->references('id')->on('users');
			$table->unique(array('annotation_id', 'id'));
		});

		DB::raw('INSERT INTO `annotation_comments_temp` SELECT * FROM `annotation_comments`');

		Schema::drop('annotation_comments');

		Schema::rename('annotation_comments_temp', 'annotation_comments');
	}

	public function downSQLite()
	{
		Schema::create('annotation_comments_temp', function($table) {
			$table->engine = "InnoDB";
			
			$table->integer('id')->unsigned();
			$table->integer('annotation_id')->unsigned();
			$table->integer('user_id')->unsigned();
			$table->string('text');
			$table->timestamps();
			
			$table->foreign('annotation_id')->references('id')->on('annotations')->on_delete('cascade');
			$table->foreign('user_id')->references('id')->on('users');
			$table->unique(array('annotation_id', 'id'));
		});

		DB::raw('INSERT INTO `annotation_comments_temp` SELECT * FROM `annotation_comments`');

		Schema::drop('annotation_comments');

		Schema::rename('annotation_comments_temp', 'annotation_comments');
	}

}

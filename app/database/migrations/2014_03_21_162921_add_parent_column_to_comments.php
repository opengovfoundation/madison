<?php

class AddParentColumnToComments extends DualMigration {

	public function upMySQL()
	{
		Schema::table('comments', function($table){
			$table->integer('parent_id')->unsigned()->after('doc_id')->nullable();

			$table->foreign('parent_id')->references('id')->on('comments')->onDelete('cascade');
		});
	}

	public function downMySQL()
	{
		Schema::table('comments', function($table){
			$table->dropForeign('comments_parent_id_foreign');
			$table->dropColumn('parent_id');
		});
	}

	public function upSQLite(){
		Schema::table('comments', function($table){
			$table->integer('parent_id')->unsigned()->after('doc_id')->nullable();

			$table->foreign('parent_id')->references('id')->on('comments')->onDelete('cascade');
		});
	}

	public function downSQLite(){
		Schema::table('comments', function($table){
			$table->dropForeign('comments_parent_id_foreign');
			$table->dropColumn('parent_id');
		});
	}

}

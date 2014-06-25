<?php

class RenameDocColumnInAnnotations extends DualMigration {

	public function upMySQL(){
		Schema::table('annotations', function($table){
			$table->dropForeign('annotations_doc_foreign');
			$table->renameColumn('doc', 'doc_id');

			$table->foreign('doc_id')->references('id')->on('docs')->onDelete('cascade');
		});
	}

	public function downMySQL(){
		Schema::table('annotations', function($table){
			$table->dropForeign('annotations_doc_id_foreign');
			$table->renameColumn('doc_id', 'doc');

			$table->foreign('doc')->references('id')->on('docs');
		});
	}

	public function upSQLite()
	{
		DB::statement('PRAGMA foreign_keys = OFF');

		Schema::create('annotations_temp', function($table) {
			$table->engine = "InnoDB";
			
			$table->increments('id');
			$table->string('search_id')->nullable();
			$table->integer('user_id')->unsigned();
			$table->integer('doc_id')->unsigned();
			$table->string('quote');
			$table->string('text');
			$table->string('uri');
			$table->timestamps();
			
			$table->foreign('user_id')->references('id')->on('users');
			$table->foreign('doc_id')->references('id')->on('docs');
			$table->unique('search_id');
			
		});

		DB::statement('INSERT INTO `annotations_temp` (`id`, `search_id`, `user_id`, `doc_id`, `quote`, `text`, `uri`, `created_at`, `updated_at`) SELECT `id`, `search_id`, `user_id`, `doc`, `quote`, `text`, `uri`, `created_at`, `updated_at` FROM `annotations`');

		Schema::drop('annotations');
		Schema::rename('annotations_temp', 'annotations');

		DB::statement('PRAGMA foreign_keys = ON');
	}

	public function downSQLite()
	{
		DB::statement('PRAGMA foreign_keys = OFF');

		Schema::create('annotations_temp', function($table) {
			$table->engine = "InnoDB";
			
			$table->increments('id');
			$table->string('search_id')->nullable();
			$table->integer('user_id')->unsigned();
			$table->integer('doc')->unsigned();
			$table->string('quote');
			$table->string('text');
			$table->string('uri');
			$table->timestamps();
			
			$table->foreign('user_id')->references('id')->on('users');
			$table->foreign('doc')->references('id')->on('docs');
			$table->unique('search_id');
			
		});

		DB::statement('INSERT INTO `annotations_temp` (`id`, `search_id`, `user_id`, `doc`, `quote`, `text`, `uri`, `created_at`, `updated_at`) SELECT `id`, `search_id`, `user_id`, `doc_id`, `quote`, `text`, `uri`, `created_at`, `updated_at` FROM `annotations`');

		Schema::drop('annotations');
		Schema::rename('annotations_temp', 'annotations');

		DB::statement('PRAGMA foreign_keys = ON');
	}

}

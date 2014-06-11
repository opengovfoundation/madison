<?php

class DropAnnotationMetaColumns extends DualMigration {

	public function upMySQL(){
		Schema::table('annotations', function($table) {
			$table->dropColumn('likes');
			$table->dropColumn('dislikes');
			$table->dropColumn('flags');
		});
	}

	public function downMySQL(){
		Schema::table('annotations', function($table){
			$table->integer('likes');
			$table->integer('dislikes');
			$table->integer('flags');
		});
	}

	public function upSQLite(){
		DB::statement('PRAGMA foreign_keys = OFF');

		Schema::create('annotations_temp', function($table) {
			$table->engine = "InnoDB";
			
			$table->increments('id');
			$table->string('search_id');
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

		DB::statement('INSERT INTO `annotations_temp` (`id`, `search_id`, `user_id`, `doc`, `quote`, `text`, `uri`, `created_at`, `updated_at`) SELECT `id`, `search_id`, `user_id`, `doc`, `quote`, `text`, `uri`, `created_at`, `updated_at` FROM `annotations`');

		Schema::dropIfExists('annotations');
		Schema::rename('annotations_temp', 'annotations');

		DB::statement('PRAGMA foreign_keys = ON');
	}

	public function downSQLite(){
		DB::statement('PRAGMA foreign_keys = OFF');
		
		Schema::create('annotations_temp', function($table) {
			$table->engine = "InnoDB";
			
			$table->increments('id');
			$table->string('search_id');
			$table->integer('user_id')->unsigned();
			$table->integer('doc')->unsigned();
			$table->string('quote');
			$table->string('text');
			$table->string('uri');
			$table->integer('likes');
			$table->integer('dislikes');
			$table->integer('flags');
			$table->timestamps();
			
			$table->foreign('user_id')->references('id')->on('users');
			$table->foreign('doc')->references('id')->on('docs');
			$table->unique('search_id');
		});

		DB::statement('INSERT INTO `annotations_temp` (`id`, `search_id`, `user_id`, `doc`, `quote`, `text`, `uri`, `created_at`, `updated_at`) SELECT `id`, `search_id`, `user_id`, `doc`, `quote`, `text`, `uri`, `created_at`, `updated_at` FROM `annotations`');
		
		Schema::drop('annotations');
		Schema::rename('annotations_temp', 'annotations');

		DB::statement('PRAGMA foreign_keys = ON');
	}
}

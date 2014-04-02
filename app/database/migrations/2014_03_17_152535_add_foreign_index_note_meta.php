<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignIndexNoteMeta extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('note_meta', function($table) {
			$table->foreign('annotation_id')->references('id')->on('annotations')->on_delete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('note_meta', function($table) {
			$table->dropIndex('note_meta_annotation_id_foreign');
		});
		
	}

}

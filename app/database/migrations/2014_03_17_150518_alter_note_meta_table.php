<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterNoteMetaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{ 
		Schema::table('note_meta', function($table) {
			$table->dropColumn('note_id');
			$table->integer('annotation_id')->unsigned();
		});
		
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		throw new Exception("Can't rollback this migration");
	}

}

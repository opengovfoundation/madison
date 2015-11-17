<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveDislikeEntries extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        // note_meta == annotation_meta
        DB::Statement("DELETE FROM `note_meta` where meta_value = 'dislike';");
        DB::Statement("DELETE FROM `comment_meta` where meta_value = 'dislike';");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// No going back!
	}

}

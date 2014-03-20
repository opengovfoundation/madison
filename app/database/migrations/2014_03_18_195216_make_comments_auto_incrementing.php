<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeCommentsAutoIncrementing extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `annotation_comments` CHANGE COLUMN `id` `id` INT(10) unsigned auto_increment primary key");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::statement("ALTER TABLE `annotation_comments` CHANGE COLUMN `id` `id` INT(10) unsigned");
	}

}

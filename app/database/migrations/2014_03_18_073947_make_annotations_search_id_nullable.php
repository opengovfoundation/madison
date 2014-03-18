<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeAnnotationsSearchIdNullable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `annotations` CHANGE COLUMN `search_id` `search_id` VARCHAR(255) NULL");
		
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `annotations` CHANGE COLUMN `search_id` `search_id` VARCHAR(255) IS NOT NULL");
	}

}

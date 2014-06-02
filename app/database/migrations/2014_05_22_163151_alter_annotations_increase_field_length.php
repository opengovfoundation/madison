<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAnnotationsIncreaseFieldLength extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement('ALTER TABLE annotations MODIFY COLUMN quote TEXT');
		DB::statement('ALTER TABLE annotations MODIFY COLUMN `text` TEXT');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement('ALTER TABLE annotations MODIFY COLUMN quote varchar(255)');
		DB::statement('ALTER TABLE annotations MODIFY COLUMN `text` varchar(255)');
	}

}

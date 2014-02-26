<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDateDocTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('date_doc', function($table){
			$table->integer('date_id')->unsigned();
			$table->integer('doc_id')->unsigned();

			$table->foreign('date_id')->references('id')->on('dates')->onDelete('cascade');
			$table->foreign('doc_id')->references('id')->on('docs')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('date_doc');
	}

}

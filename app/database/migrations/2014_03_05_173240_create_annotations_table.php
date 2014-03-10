<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnnotationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('annotations', function($table) {
			$table->engine = "InnoDB";
			$table->string('id');
			$table->integer('user_id')->unsigned();
			$table->integer('doc')->unsigned();
			$table->string('quote');
			$table->string('text');
			$table->string('uri');
			$table->integer('likes');
			$table->integer('dislikes');
			$table->integer('flags');
			$table->timestamps();
			
			$table->primary('id');
			$table->foreign('user_id')->references('id')->on('users');
			$table->foreign('doc')->references('id')->on('docs');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('annotations');
	}

}

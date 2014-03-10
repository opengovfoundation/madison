<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnnotationCommentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('annotation_comments', function($table) {
			$table->engine = "InnoDB";
			
			$table->integer('id')->unsigned();
			$table->string('annotation_id');
			$table->integer('user_id')->unsigned();
			$table->string('text');
			$table->timestamps();
			
			$table->foreign('annotation_id')->references('id')->on('annotations');
			$table->foreign('user_id')->references('id')->on('users');
			$table->unique(array('annotation_id', 'id'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('annotation_comments');
	}

}

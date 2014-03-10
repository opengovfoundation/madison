<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnnotationPermissionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('annotation_permissions', function($table) {

			$table->engine = "InnoDB";
			
			$table->increments('id');
			$table->string('annotation_id');
			$table->integer('user_id')->unsigned();
			$table->integer('read');
			$table->integer('update');
			$table->integer('delete');
			$table->integer('admin');
			$table->timestamps();
			
			$table->foreign('user_id')->references('id')->on('users');
			$table->foreign('annotation_id')->references('id')->on('annotations');
			$table->unique(array('user_id', 'annotation_id'));
			
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('annotation_permissions');
	}

}

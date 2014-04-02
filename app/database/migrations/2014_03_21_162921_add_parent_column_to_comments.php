<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParentColumnToComments extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('comments', function($table){
			$table->integer('parent_id')->unsigned()->after('doc_id')->nullable();

			$table->foreign('parent_id')->references('id')->on('comments')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('comments', function($table){
			$table->dropColumn('parent_id');
		});
	}

}

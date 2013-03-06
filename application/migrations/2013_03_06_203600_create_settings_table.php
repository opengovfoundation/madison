<?php

class Create_Settings_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('settings', function($table){
			$table->engine = "InnoDB";
			$table->increments('id');
			$table->string('meta_key');
			$table->string('meta_value')->nullable();
			$table->timestamps();
		});
		
		DB::table('settings')->insert(
			array(
				'meta_key' => 'title',
				'meta_value' => ''
			),
			array(
				'meta_key' => 'nav_menu',
				'meta_value' => ''
			),
			array(
				'meta_key' => 'fb_app_id',
				'meta_value' => ''
			),
			array(
				'meta_key' => 'fb_app_secret',
				'meta_value' => ''
			)
		);
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('settings');
	}

}
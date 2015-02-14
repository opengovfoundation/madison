<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		$this->call('UsersTableSeeder');
		$this->call('RbacSeeder');
		$this->call('GroupsTableSeeder');
		$this->call('DocumentsTableSeeder');
		if (App::environment() === 'testing') {
			$this->call('TestSeeder');
		}
	}

}

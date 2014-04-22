<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder 
{
	public function run()
	{
		DB::table('users')->insert(array(
			'email' => 'john@coggeshall.org',
			'password' => '$2y$10$uIX./LUQwWBW3Orqd.E7LOY8KdCHHkIM9dGmZe95lFlf0OrH8YzOK',
			'fname' => 'John',
			'lname' => 'Coggeshall',
			'token' => '',
		));
	}
}

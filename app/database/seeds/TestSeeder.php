<?php

use Illuminate\Database\Seeder;

class TestingSeeder extends Seeder 
{
	public function run() {
		$creds = array(
			'test_email' => 'test@example.com',
			'test_fname' => 'First',
			'test_lname' => 'Last',
			'test_password' => 'password'
		);

		DB::table('users')->insert(array(
			'email' => $creds['test_email'],
			'password' => Hash::make($creds['test_password']),
			'fname' => $creds['test_fname'],
			'lname' => $creds['test_lname'],
			'token' => '',
		));
	}
}

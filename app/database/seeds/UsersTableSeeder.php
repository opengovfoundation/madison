<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder 
{
	public function run()
	{
		if(file_exists(app_path() . '/config/creds.yml')){
			$creds = yaml_parse_file(app_path() . '/config/creds.yml');
		}else{
			$creds = array(
			  'admin_email' => 'test@example.com',
			  'admin_fname' => 'First',
			  'admin_lname' => 'Last',
			  'admin_password' => 'password'
			);
		}

		DB::table('users')->insert(array(
			'email' => $creds['admin_email'],
			'password' => Hash::make($creds['admin_password']),
			'fname' => $creds['admin_fname'],
			'lname' => $creds['admin_lname'],
			'token' => '',
		));
	}
}

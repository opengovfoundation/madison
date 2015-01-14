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
			  'admin_email' => 'admin@example.com',
			  'admin_fname' => 'Venustiano',
			  'admin_lname' => 'Carranza',
			  'admin_password' => 'password',
			  'user_email' => 'user@example.com',
			  'user_fname' => 'John',
			  'user_lname' => 'Appleseed',
			  'user_password' => 'password'
			);
		}

		DB::table('users')->insert(array(
			'email' => $creds['admin_email'],
			'password' => Hash::make($creds['admin_password']),
			'fname' => $creds['admin_fname'],
			'lname' => $creds['admin_lname'],
			'token' => '',
		));
    DB::table('users')->insert(array(
      'email' => $creds['user_email'],
      'password' => Hash::make($creds['user_password']),
      'fname' => $creds['user_fname'],
      'lname' => $creds['user_lname'],
      'token' => '',
    ));
	}
}

<?php

use Illuminate\Database\Seeder;

class GroupsTableSeeder extends Seeder
{
	public function run()
	{
		if(file_exists(app_path() . '/config/creds.yml')) {
			$creds = yaml_parse_file(app_path() . '/config/creds.yml');
		}

    $group = new Group();
    $group->status = Group::STATUS_ACTIVE;
		$group->name = 'México Abierto';
    $group->display_name = 'mx_a';
		$group->save();

    // Login as admin to add members to group
		$credentials = array('email' => $creds['admin_email'], 'password' => $creds['admin_password']);
    Auth::attempt($credentials);

		$admin = User::where('email', '=', $creds['admin_email'])->first();
		$user = User::where('email', '=', $creds['user_email'])->first();

		$group->addMember($admin->id, Group::ROLE_OWNER);
		$group->addMember($user->id, Group::ROLE_EDITOR);
	}
}

<?php

use Illuminate\Database\Seeder;

class RbacSeeder extends Seeder
{
	private $adminPermissions = array(
		'ManageDocuments' => array(
			'name' => 'admin_manage_documents',
			'display_name' => 'Manage Documents',
		),
		'ManageSettings' => array(
			'name' => 'admin_manage_settings',
			'display_name' => "Manage Settings"
		),
		'VerifyUsers' => array(
			'name' => "admin_verify_users",
			'display_name' => "Verify Users"
		),
	);
	
	public function run()
	{
		if(file_exists(app_path() . '/config/creds.yml')){
			$creds = yaml_parse_file(app_path() . '/config/creds.yml');
		}else{
			$creds = array(
			  'admin_email' => 'admin@example.com',
			);
		}

		$admin = new Role();
		$admin->name = 'Admin';
		$admin->save();

		$independent_sponsor = new Role();
		$independent_sponsor->name = 'Independent Sponsor';
		$independent_sponsor->save();
		
		$permIds = array();
		foreach($this->adminPermissions as $permClass => $data) {
			$perm = new Permission();

			foreach($data as $key => $val) {
				$perm->$key = $val;
			}
				
			$perm->save();
				
			$permIds[] = $perm->id;
		}
		
		$admin->perms()->sync($permIds);
		
		$user = User::where('email', '=', $creds['admin_email'])->first();
		$user->attachRole($admin);
		
		$createDocPerm = new Permission();
		$createDocPerm->name = "independent_sponsor_create_doc";
		$createDocPerm->display_name = "Independent Sponsoring";
		$createDocPerm->save();
		
		$independent_sponsor->perms()->sync(array($createDocPerm->id));
	}
}
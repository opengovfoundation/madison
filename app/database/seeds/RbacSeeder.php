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
		)
	);
	
	public function run()
	{
		$admin = new Role();
		$admin->name = 'Admin';
		$admin->save();
		
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
		
		$user = User::where('email', '=', 'john@coggeshall.org')->first();
		$user->attachRole($admin);
	}
}
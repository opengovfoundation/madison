<?php

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use App\Models\Sponsor;

class RbacSeeder extends Seeder
{
    private $adminPermissions = array(
        'ManageDocuments' => array(
            'name' => 'admin_manage_documents',
            'display_name' => 'Manage Documents',
        ),
        'ManageSettings' => array(
            'name' => 'admin_manage_settings',
            'display_name' => "Manage Settings",
        ),
        'VerifyUsers' => array(
            'name' => "admin_verify_users",
            'display_name' => "Verify Users",
        ),
    );

    public function run()
    {
        $adminEmail = Config::get('madison.seeder.admin_email');
        $adminRole = Role::adminRole();

        $permIds = array();
        foreach ($this->adminPermissions as $permClass => $data) {
            $perm = new Permission();

            foreach ($data as $key => $val) {
                $perm->$key = $val;
            }

            $perm->save();

            $permIds[] = $perm->id;
        }

        $adminRole->perms()->sync($permIds);

        $user = User::where('email', '=', $adminEmail)->first();
        $user->makeAdmin();
    }
}

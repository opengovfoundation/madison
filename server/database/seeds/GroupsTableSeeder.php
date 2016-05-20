<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Group;

class GroupsTableSeeder extends Seeder
{
    public function run()
    {
        $group = new Group();
        $group->status = Group::STATUS_ACTIVE;
        $group->name = 'Example Group';
        $group->display_name = 'Example Group Display';
        $group->address1 = '1234 Somewhere';
        $group->city = 'City';
        $group->state = 'DC';
        $group->postal_code = '12345';
        $group->phone = '555-555-5555';
        $group->save();

        $adminEmail = Config::get('madison.seeder.admin_email');
        $adminPassword = Config::get('madison.seeder.admin_password');

        $userEmail = Config::get('madison.seeder.user_email');

        // Login as admin to add members to group
        $credentials = array('email' => $adminEmail, 'password' => $adminPassword);

        Auth::attempt($credentials);

        $admin = User::where('email', '=', $adminEmail)->first();
        $user = User::where('email', '=', $userEmail)->first();

        $group->addMember($admin->id, Group::ROLE_OWNER);
        $group->addMember($user->id, Group::ROLE_EDITOR);
    }
}

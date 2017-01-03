<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Sponsor;

class SponsorsTableSeeder extends Seeder
{
    public function run()
    {
        $sponsor = new Sponsor();
        $sponsor->status = Sponsor::STATUS_ACTIVE;
        $sponsor->name = 'Example Sponsor';
        $sponsor->display_name = 'Example Sponsor Display';
        $sponsor->address1 = '1234 Somewhere';
        $sponsor->city = 'City';
        $sponsor->state = 'DC';
        $sponsor->postal_code = '12345';
        $sponsor->phone = '555-555-5555';
        $sponsor->save();

        $adminEmail = Config::get('madison.seeder.admin_email');
        $adminPassword = Config::get('madison.seeder.admin_password');

        $userEmail = Config::get('madison.seeder.user_email');

        // Login as admin to add members to sponsor
        $credentials = array('email' => $adminEmail, 'password' => $adminPassword);

        Auth::attempt($credentials);

        $admin = User::where('email', '=', $adminEmail)->first();
        $user = User::where('email', '=', $userEmail)->first();

        $sponsor->addMember($admin->id, Sponsor::ROLE_OWNER);
        $sponsor->addMember($user->id, Sponsor::ROLE_EDITOR);
    }
}

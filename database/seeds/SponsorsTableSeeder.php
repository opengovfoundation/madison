<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Sponsor;

class SponsorsTableSeeder extends Seeder
{
    public function run()
    {
        $sponsor = factory(Sponsor::class)->create([
            'status' => Sponsor::STATUS_ACTIVE,
        ]);

        $adminEmail = Config::get('madison.seeder.admin_email');
        $userEmail = Config::get('madison.seeder.user_email');

        $admin = User::where('email', '=', $adminEmail)->first();
        $user = User::where('email', '=', $userEmail)->first();

        $sponsor->addMember($admin->id, Sponsor::ROLE_OWNER);
        $sponsor->addMember($user->id, Sponsor::ROLE_EDITOR);
    }
}

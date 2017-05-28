<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Sponsor;

class SponsorsTableSeeder extends Seeder
{
    public function run()
    {
        $adminEmail = Config::get('madison.seeder.admin_email');
        $userEmail = Config::get('madison.seeder.user_email');

        $admin = User::where('email', '=', $adminEmail)->first();
        $user = User::where('email', '=', $userEmail)->first();

        factory(Sponsor::class, config('madison.seeder.num_active_sponsors'))->create([
            'status' => Sponsor::STATUS_ACTIVE
        ])->each(function ($sponsor) use ($admin, $user) {
            $sponsor->addMember($admin->id, Sponsor::ROLE_OWNER);
            $sponsor->addMember($user->id, Sponsor::ROLE_EDITOR);
        });

        factory(Sponsor::class, config('madison.seeder.num_pending_sponsors'))->create([
            'status' => Sponsor::STATUS_PENDING
        ])->each(function ($sponsor) use ($admin, $user) {
            $sponsor->addMember($admin->id, Sponsor::ROLE_OWNER);
            $sponsor->addMember($user->id, Sponsor::ROLE_EDITOR);
        });
    }
}

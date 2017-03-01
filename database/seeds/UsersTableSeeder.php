<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $creds = Config::get('madison.seeder');

        factory(User::class)->create([
            'email' => $creds['user_email'],
            'password' => $creds['user_password'],
            'fname' => $creds['user_fname'],
            'lname' => $creds['user_lname'],
        ]);

        factory(User::class)->create([
            'email' => $creds['admin_email'],
            'password' => $creds['admin_password'],
            'fname' => $creds['admin_fname'],
            'lname' => $creds['admin_lname'],
        ]);

        factory(User::class)->states('emailUnverified')->create([
            'email' => $creds['unconfirmed_email'],
            'password' => $creds['unconfirmed_password'],
            'fname' => $creds['unconfirmed_fname'],
            'lname' => $creds['unconfirmed_lname'],
        ]);
    }
}

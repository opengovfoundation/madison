<?php

use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    public function run()
    {
        $test_fname = "Alice";
        $test_lname = "Wonderland";
        $test_password = 'password';

        // Sample user, confirmed email, id 2
        DB::table('users')->insert(array(
            'email' => 'test@opengovfoundation.org',
            'password' => Hash::make($test_password),
            'fname' => $test_fname,
            'lname' => $test_lname,
            'token' => '',
        ));

        // Sample user, unconfirmed email, id 3
        DB::table('users')->insert(array(
            'email' => 'test2@opengovfoundation.org',
            'password' => Hash::make($test_password),
            'fname' => $test_fname,
            'lname' => $test_lname,
            'token' => '12345',
        ));
    }
}

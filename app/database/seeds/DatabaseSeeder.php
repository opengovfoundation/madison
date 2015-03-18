<?php

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Eloquent::unguard();

        $this->call('UsersTableSeeder');
        $this->call('RbacSeeder');
        if (App::environment() === 'testing') {
            $this->call('TestSeeder');
        }
    }
}

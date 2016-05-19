<?php

namespace App\Console\Commands;

use App\Models\User;
use Hash;
use Illuminate\Console\Command;

class CreateUser extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'user:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Artisan command used to create users.';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $this->line('Creating user.');
        $email = $this->ask('What is the user\'s email?');
        $password = $this->ask('What is the user\'s password?');
        $fname = $this->ask('What is the user\'s first name?');
        $lname = $this->ask('What is the user\'s last name?');

        $user = new User();
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->fname = $fname;
        $user->lname = $lname;
        $user->token = '';

        if ($this->confirm("You are about to create user:\n--------------------\nEmail: {$user->email}\nPassword: $password ({$user->password})\nName: {$user->fname} {$user->lname}\n--------------------\n Continue? [yes|no]")) {
            $user->save();

            $this->info('User created.');
        } else {
            $this->info('User creation cancelled...');
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

class UserRole extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'user:addRole';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a role to a user.';

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
        $roles = Role::all();

        $email = $this->ask("User's email address:");
        $user = User::where('email', $email)->first();

        if (!isset($user)) {
            $this->error("No user with email $email found!");

            return;
        }

        $roleOptions = "";
        foreach ($roles as $role) {
            $roleOptions .= "{$role->id} - {$role->name}\n";
        }

        $this->info("\nRole Options: \n".$roleOptions);
        $roleId = $this->ask("Choose role (enter number): ");

        $role = Role::find($roleId);

        if (!isset($role)) {
            $this->error("Role id $roleId not found!");

            return;
        }

        $user->attachRole($role);

        $this->info("Role {$role->name} added to user with email {$user->email}");
    }
}

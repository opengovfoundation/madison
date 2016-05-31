<?php

namespace App\Console\Commands;

use App\Models\Role;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class CreateRole extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'role:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new role.';

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
        if ($this->argument('name')) {
            $name = trim($this->argument('name'));

            $role = Role::where('name', '=', $name)->first();

            if ($role) {
                $this->info("Role '$name' already exists.");
                exit;
            }

            $role = new Role();
            $role->name = $name;
            $role->save();

            $this->info('Role saved successfully.');
        } else {
            $roles = Role::all();

            $this->info('Existing Roles:');

            foreach ($roles as $role) {
                $this->info($role->name);
            }

            $continue = $this->ask("Would you still like to add a new role? (yes/no)");

            if ('yes' === trim(strtolower($continue))) {
                $name = $this->ask("What's the new role's name?");
                $role = new Role();
                $role->name = trim($name);
                $role->save();

                $this->info('Role saved successfully.');
            }
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('name', InputArgument::OPTIONAL, 'The role name to create'),
        );
    }
}

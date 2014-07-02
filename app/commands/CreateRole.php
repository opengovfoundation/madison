<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CreateRole extends Command {

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
	 *
	 * @return void
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

		$this->info('Existing Roles:');

		foreach($roles as $role){
			$this->info($role->name);
		}

		$continue = $this->ask("Would you still like to add a new role? (yes/no)");

		if('yes' === trim(strtolower($continue))){
			$name = $this->ask("What's the new role's name?");
			$role = new Role();
			$role->name = trim($name);
			$role->save();

			$this->info('Role saved successfully.');
		}
	}
}

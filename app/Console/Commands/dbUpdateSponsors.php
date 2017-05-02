<?php

namespace App\Console\Commands;

use App\Models\Role;
use Illuminate\Console\Command;

class dbUpdateSponsors extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'db:update_sponsors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update a live database with changes from sponsors additions.';

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
        //1.  Create Independent Sponsor role
            $sponsorRole = Role::where('name', '=', 'Independent Sponsor')->first();

        if (!$sponsorRole) {
            $sponsorRole = new Role();
            $sponsorRole->name = "Independent Sponsor";
            $sponsorRole->save();

            $this->info("Independent Sponsor role created.");
        } else {
            $this->info("Independent Sponsor role exists.");
        }

        //2. Add Independent Sponsor role to all Admins
            $adminRole = Role::where('name', 'Admin')->first();
        $admins = $adminRole->users()->get();

        foreach ($admins as $admin) {
            $this->info('--------------------------------------------------');
            if ($admin->hasRole($sponsorRole->name)) {
                $this->info($admin->email." already set as Independent Sponsor");
            } else {
                $admin->attachRole($sponsorRole);
                $this->info($admin->email." set as ".$sponsorRole->name);
            }

                //3.  Remove Admin role from non-admin users
                $stayAdmin = strtolower(trim($this->ask("Keep ".$admin->email." as an Admin? (yes/no)")));

            if ($stayAdmin != 'yes') {
                $admin->detachRole($adminRole);
                $this->info("Removed Admin role from ".$admin->email);
            } else {
                $this->info($admin->email." still set as an Admin.");
            }
        }
    }
}

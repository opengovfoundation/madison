<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DatabaseRebuild extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'db:rebuild {--database=mysql}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drops and recreates the database.';
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
        $database = \Config::get('database')['connections'][$this->option('database')]['database'];
        //\DB::statement('DROP DATABASE IF EXISTS '.$database);
        \DB::statement('CREATE DATABASE '.$database);
    }
}

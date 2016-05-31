<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class DatabaseRebuild extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:rebuild {--database=mysql : The database connection to use}';

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
        \DB::statement('DROP DATABASE IF EXISTS '.$database);
        \DB::statement('CREATE DATABASE '.$database);
    }
}

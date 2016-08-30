<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class DatabaseRestore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:restore {filename} {--database=mysql : The database connection to use}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restores the database from a mysql dump file.';

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
        $filename = $this->argument('filename');

        // Make sure file option is a valid file
        if (!file_exists($filename)) {
            throw new Exception('Must provide a valid file path.');
        }

        // Recreate the database first
        \DB::statement('DROP DATABASE IF EXISTS '.$database);
        \DB::statement('CREATE DATABASE '.$database);

        if (empty($_ENV['DB_PASSWORD'])) {
            $pass = '';
        } else {
            $pass = '-p'.$_ENV['DB_PASSWORD'];
        }

        exec('mysql '.$_ENV['DB_DATABASE'].' -u'.$_ENV['DB_USERNAME'].$pass.' < '.$filename);
    }
}

<?php

use Illuminate\Console\Command;

class DatabaseClear extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'db:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clears the database.';

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
        $database = Config::get('database')['connections']['mysql']['database'];

        $tables = DB::select("select * from information_schema.tables where table_schema='".$database."'");
        DB::statement('SET foreign_key_checks = 0');

        foreach ($tables as $table) {
            Schema::drop($table->TABLE_NAME);
        }

        DB::statement('SET foreign_key_checks = 1');
    }
}

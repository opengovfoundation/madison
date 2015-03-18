<?php

use Illuminate\Database\Migrations\Migration;

abstract class DualMigration extends Migration implements DualMigrationInterface
{
  public function up()
  {
      $connection = DB::connection()->getDriverName();

      switch ($connection) {
      case 'mysql':
        $this->upMySQL();
        break;

      case 'sqlite':
        $this->upSQLite();
        break;

      default:
        throw new Exception("Unknown connection $connection");
    }
  }

    public function down()
    {
        $connection = DB::connection()->getDriverName();

        switch ($connection) {
      case 'mysql':
        $this->downMySQL();
        break;

      case 'sqlite':
        $this->downSQLite();
        break;

      default:
        throw new Exception("Unknown connection $connection");
    }
    }

    abstract public function upMySQL();
    abstract public function downMySQL();
    abstract public function upSQLite();
    abstract public function downSQLite();
}

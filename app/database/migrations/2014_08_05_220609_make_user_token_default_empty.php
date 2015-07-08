<?php

use Illuminate\Database\Migrations\Migration;

class MakeUserTokenDefaultEmpty extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::statement("ALTER TABLE users CHANGE token token varchar(25) NOT NULL DEFAULT ''");
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::statement('ALTER TABLE users CHANGE token token varchar(25) NOT NULL');
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakePasswordNullable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Make passwords & emails NULLable
        DB::statement('ALTER TABLE users CHANGE password password varchar(100)');
        DB::statement('ALTER TABLE users CHANGE email email varchar(255)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Make passwords & emails not-null
        DB::statement('ALTER TABLE users CHANGE password password varchar(100) NOT NULL');
        DB::statement('ALTER TABLE users CHANGE email email varchar(255) NOT NULL');
    }
}

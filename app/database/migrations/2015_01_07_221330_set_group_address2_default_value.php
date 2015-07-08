<?php

use Illuminate\Database\Migrations\Migration;

class SetGroupAddress2DefaultValue extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::statement('ALTER TABLE groups CHANGE address2 address2 varchar(255) DEFAULT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::statement('ALTER TABLE groups CHANGE address2 address2 varchar(255) NOT NULL');
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropUserLevelColumn extends Migration
{
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('user_level');
        });
    }

    public function down()
    {
        Schema::table('users', function ($table) {
            $table->integer('user_level');
        });
    }
}

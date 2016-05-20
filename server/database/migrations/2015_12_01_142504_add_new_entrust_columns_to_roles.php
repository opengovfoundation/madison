<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewEntrustColumnsToRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('roles', function ($table) {
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roles', function ($table) {
            $table->dropColumn('display_name');
            $table->dropColumn('description');
        });
    }
}

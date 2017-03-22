<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropOrganizations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('organizations');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('organizations', function ($table) {
            $table->engine = "InnoDB";
            $table->increments('id');
            $table->string('name');
            $table->string('zip');
            $table->string('url');
            $table->timestamps();
        });
    }
}

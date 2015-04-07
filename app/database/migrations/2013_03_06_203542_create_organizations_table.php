<?php

use Illuminate\Database\Migrations\Migration;

class CreateOrganizationsTable extends Migration
{
    /**
     * Make changes to the database.
     */
    public function up()
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

    /**
     * Revert the changes to the database.
     */
    public function down()
    {
        Schema::drop('organizations');
    }
}

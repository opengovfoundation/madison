<?php

use Illuminate\Database\Migrations\Migration;

class CreateDocsTable extends Migration
{
    /**
     * Make changes to the database.
     */
    public function up()
    {
        Schema::create('docs', function ($table) {
            $table->engine = "InnoDB";
            $table->increments('id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('shortname')->nullable();
            $table->integer('init_section')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Revert the changes to the database.
     */
    public function down()
    {
        Schema::drop('docs');
    }
}

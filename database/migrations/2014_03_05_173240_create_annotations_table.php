<?php

use Illuminate\Database\Migrations\Migration;

class CreateAnnotationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('annotations', function ($table) {
            $table->engine = "InnoDB";

            $table->increments('id');
            $table->string('search_id');
            $table->integer('user_id')->unsigned();
            $table->integer('doc')->unsigned();
            $table->string('quote');
            $table->string('text');
            $table->string('uri');
            $table->integer('likes');
            $table->integer('dislikes');
            $table->integer('flags');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('doc')->references('id')->on('docs');
            $table->unique('search_id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('annotations');
    }
}

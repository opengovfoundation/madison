<?php

use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('comments', function ($table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('doc_id')->unsigned();
            $table->text('content');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->on_delete('cascade');
            $table->foreign('doc_id')->references('id')->on('docs')->on_delete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('users');
    }
}

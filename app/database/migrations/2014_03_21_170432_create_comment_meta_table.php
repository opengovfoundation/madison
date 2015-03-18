<?php

use Illuminate\Database\Migrations\Migration;

class CreateCommentMetaTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('comment_meta', function ($table) {
            $table->increments('id');
            $table->integer('comment_id')->unsigned();
            $table->integer('user_id')->unsigned()->nullable();
            $table->string('meta_key');
            $table->string('meta_value')->nullable();
            $table->timestamps();

            //Set foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('comment_id')->references('id')->on('comments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('comment_meta');
    }
}

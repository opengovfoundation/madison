<?php

use Illuminate\Database\Migrations\Migration;

class AddUserMetaTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('user_meta', function ($table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('meta_key');
            $table->string('meta_value');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->on_delete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('user_meta');
    }
}

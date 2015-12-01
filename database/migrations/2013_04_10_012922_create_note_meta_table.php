<?php

use Illuminate\Database\Migrations\Migration;

class CreateNoteMetaTable extends Migration
{
    /**
     * Make changes to the database.
     */
    public function up()
    {
        Schema::create('note_meta', function ($table) {
            $table->engine = "InnoDB";
            $table->increments('id');
            $table->string('note_id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->string('meta_key');
            $table->string('meta_value')->nullable();
            $table->timestamps();

            //Set foreign keys
            $table->foreign('user_id')->references('id')->on('users')->on_delete('cascade');
        });
    }

    /**
     * Revert the changes to the database.
     */
    public function down()
    {
        Schema::drop('note_meta');
    }
}

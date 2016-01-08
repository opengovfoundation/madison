<?php

use Illuminate\Database\Migrations\Migration;

class CreateDocUserTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('doc_user', function ($table) {
            $table->integer('doc_id')->unsigned();
            $table->integer('user_id')->unsigned();

            $table->foreign('doc_id')->references('id')->on('docs')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('doc_user');
    }
}

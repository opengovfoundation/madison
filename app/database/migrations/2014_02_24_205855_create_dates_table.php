<?php

use Illuminate\Database\Migrations\Migration;

class CreateDatesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('dates', function ($table) {
            $table->increments('id');
            $table->integer('doc_id')->unsigned();
            $table->datetime('date');
            $table->string('label');
            $table->timestamps();

            $table->foreign('doc_id')->references('id')->on('docs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('dates');
    }
}

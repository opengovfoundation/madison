<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropDates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('dates');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
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
}

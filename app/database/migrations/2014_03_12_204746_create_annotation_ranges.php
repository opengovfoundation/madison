<?php

use Illuminate\Database\Migrations\Migration;

class CreateAnnotationRanges extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('annotation_ranges', function ($table) {

            $table->engine = "InnoDB";

            $table->increments('id');
            $table->integer('annotation_id')->unsigned();
            $table->string('start');
            $table->string('end');
            $table->integer('start_offset')->unsigned();
            $table->integer('end_offset')->unsigned();
            $table->timestamps();

            $table->foreign('annotation_id')->references('id')->on('annotations')->on_delete('cascade');
            $table->unique(array('annotation_id', 'start_offset'));
            $table->unique(array('annotation_id', 'end_offset'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('annotation_ranges');
    }
}

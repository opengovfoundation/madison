<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnnotationTagsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('annotation_tags', function ($table) {
            $table->engine = "InnoDB";
            
            $table->increments('id');
            $table->integer('annotation_id')->unsigned();
            $table->string('tag');
            $table->timestamps();
            
            $table->foreign('annotation_id')->references('id')->on('annotations')->on_delete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('annotation_tags');
    }
}

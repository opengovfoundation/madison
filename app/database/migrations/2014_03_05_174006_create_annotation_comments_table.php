<?php

use Illuminate\Database\Migrations\Migration;

class CreateAnnotationCommentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('annotation_comments', function ($table) {
            $table->engine = "InnoDB";

            $table->integer('id')->unsigned();
            $table->integer('annotation_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->string('text');
            $table->timestamps();

            $table->foreign('annotation_id')->references('id')->on('annotations')->on_delete('cascade');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unique(array('annotation_id', 'id'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('annotation_comments');
    }
}

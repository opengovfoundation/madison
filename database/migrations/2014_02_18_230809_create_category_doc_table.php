<?php

use Illuminate\Database\Migrations\Migration;

class CreateCategoryDocTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('category_doc', function ($table) {
            $table->integer('doc_id')->unsigned();
            $table->integer('category_id')->unsigned();

            $table->foreign('doc_id')->references('id')->on('docs')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('category_doc');
    }
}

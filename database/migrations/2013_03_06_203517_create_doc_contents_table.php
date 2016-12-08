<?php

use Illuminate\Database\Migrations\Migration;

class CreateDocContentsTable extends Migration
{
    /**
     * Make changes to the database.
     */
    public function up()
    {
        Schema::create('doc_contents', function ($table) {
            $table->engine = "InnoDB";
            $table->increments('id');
            $table->integer('doc_id')->unsigned();
            $table->integer('parent_id')->unsigned()->nullable();
            $table->integer('child_priority')->unsigned()->default(0);
            $table->text('content');
            $table->timestamps();

            //Set foreign keys
            $table->foreign('doc_id')->references('id')->on('docs')->on_delete('cascade');
            $table->foreign('parent_id')->references('id')->on('doc_contents')->on_delete('cascade');
        });
    }

    /**
     * Revert the changes to the database.
     */
    public function down()
    {
        Schema::drop('doc_contents');
    }
}

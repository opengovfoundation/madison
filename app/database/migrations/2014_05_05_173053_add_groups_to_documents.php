<?php

use Illuminate\Database\Migrations\Migration;

class AddGroupsToDocuments extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('doc_group', function ($table) {
            $table->integer('doc_id')->unsigned();
            $table->integer('group_id')->unsigned();

            $table->foreign('doc_id')->references('id')->on('docs')->onDelete('cascade');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('doc_group');
    }
}

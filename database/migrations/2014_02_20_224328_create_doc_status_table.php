<?php

use Illuminate\Database\Migrations\Migration;

class CreateDocStatusTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('doc_status', function ($table) {
            $table->integer('doc_id')->unsigned();
            $table->integer('status_id')->unsigned();

            $table->foreign('doc_id')->references('id')->on('docs')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('doc_status');
    }
}

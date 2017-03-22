<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropStatuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('doc_status');
        Schema::drop('statuses');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('statuses', function ($table) {
            $table->increments('id');
            $table->string('label');
            $table->timestamps();
        });

        Schema::create('doc_status', function ($table) {
            $table->integer('doc_id')->unsigned();
            $table->integer('status_id')->unsigned();

            $table->foreign('doc_id')->references('id')->on('docs')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('cascade');
        });
    }
}

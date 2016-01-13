<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePublishStates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publish_states', function (Blueprint $table) {
            $table->increments('id');
            $table->string('value');
        });

        DB::insert("insert into publish_states set value = 'unpublished'");
        DB::insert("insert into publish_states set value = 'published'");
        DB::insert("insert into publish_states set value = 'private'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('publish_states');
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doc_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('style');
            $table->string('context');
        });

        Schema::table('docs', function (Blueprint $table) {
            $table->integer('type_id')->unsigned()->nullable();
            $table->index('type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('docs', function( Blueprint $table) {
            $table->dropIndex('docs_type_id_index');
            $table->dropColumn('type_id');
        });

        Schema::drop('doc_types');
    }
}

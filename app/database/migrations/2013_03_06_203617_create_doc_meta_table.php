<?php

use Illuminate\Database\Migrations\Migration;

class CreateDocMetaTable extends Migration
{
    /**
     * Make changes to the database.
     */
    public function up()
    {
        Schema::create('doc_meta', function ($table) {
            $table->engine = "InnoDB";
            $table->increments('id');
            $table->integer('doc_id')->unsigned();
            $table->string('meta_key');
            $table->string('meta_value')->nullable();
            $table->timestamps();

            //Set foreign keys
            $table->foreign('doc_id')->references('id')->on('docs')->on_delete('cascade');
        });
    }

    /**
     * Revert the changes to the database.
     */
    public function down()
    {
        Schema::drop('doc_meta');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Make changes to the database.
     */
    public function up()
    {
        Schema::create('settings', function ($table) {
            $table->engine = "InnoDB";
            $table->increments('id');
            $table->string('meta_key');
            $table->string('meta_value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Revert the changes to the database.
     */
    public function down()
    {
        Schema::drop('settings');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Make changes to the database.
     */
    public function up()
    {
        Schema::create('users', function ($table) {
            $table->engine = "InnoDB";
            $table->increments('id');
            $table->string('email')->unique();
            $table->string('password', 100);
            $table->string('fname');
            $table->string('lname');
            $table->string('phone')->nullable();
            $table->integer('org_id')->unsigned()->nullable();
            $table->string('position')->nullable();
            $table->string('location')->nullable();
            $table->string('url')->nullable();
            $table->integer('user_level')->unsigned()->default(3);
            $table->string('token', 25);
            $table->text('likes')->nullable();
            $table->text('dislikes')->nullable();
            $table->text('flags')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->timestamps();

            //Set foreign keys
            $table->foreign('org_id')->references('id')->on('organizations')->on_delete('set null');
        });
    }

    /**
     * Revert the changes to the database.
     */
    public function down()
    {
        Schema::drop('users');
    }
}

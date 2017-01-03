<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Sponsor;

class AddGroupTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('groups', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->string('address1');
            $table->string('address2');
            $table->string('city');
            $table->string('state');
            $table->string('postal_code');
            $table->string('phone_number');
            $table->string('display_name');
            $table->enum('status', Sponsor::getStatuses());

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('group_members', function ($table) {
            $table->increments('id');
            $table->integer('group_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->enum('role', Sponsor::getRoles());
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('groups');
        Schema::drop('group_members');
    }
}

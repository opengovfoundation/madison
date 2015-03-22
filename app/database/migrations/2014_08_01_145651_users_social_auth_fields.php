<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UsersSocialAuthFields extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->string('oauth_vendor', 16)->nullable();
            $table->string('oauth_id')->nullable();
            $table->boolean('oauth_update')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('oauth_vendor');
            $table->dropColumn('oauth_id');
            $table->dropColumn('oauth_update');
        });
    }
}

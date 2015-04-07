<?php

use Illuminate\Database\Migrations\Migration;

class AddIndependentSponsorFieldsToUsers extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->string('address1')->nullable()->after('lname');
            $table->string('address2')->nullable()->after('address1');
            $table->string('city')->nullable()->after('address2');
            $table->string('state')->nullable()->after('city');
            $table->string('postal_code')->nullable()->after('state');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('address1');
            $table->dropColumn('address2');
            $table->dropColumn('city');
            $table->dropColumn('state');
            $table->dropColumn('postal_code');
        });
    }
}

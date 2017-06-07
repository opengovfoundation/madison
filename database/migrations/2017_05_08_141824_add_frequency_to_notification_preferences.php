<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFrequencyToNotificationPreferences extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notification_preferences', function ($table) {
            $table->enum('frequency', ['immediately', 'daily'])->default('immediately');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notification_preferences', function ($table) {
            $table->dropColumn('frequency');
        });
    }
}

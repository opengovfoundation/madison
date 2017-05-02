<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateIndexesOnNotificationPreferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notification_preferences', function ($table) {

            $table->dropForeign('notifications_group_id_foreign');
            $table->dropIndex('notifications_group_id_foreign');

            // rebuild foreign key for sponsor_id
            $table->foreign('sponsor_id')->references('id')->on('sponsors')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->index('sponsor_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

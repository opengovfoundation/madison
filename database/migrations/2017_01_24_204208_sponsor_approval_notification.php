<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SponsorApprovalNotification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('notification_preferences')
            ->where('event', 'madison.sponsor.created')
            ->update(['event' => 'madison.sponsor.needs_approval'])
            ;
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('notification_preferences')
            ->where('event', 'madison.sponsor.needs_approval')
            ->update(['event' => 'madison.sponsor.created'])
            ;
    }
}

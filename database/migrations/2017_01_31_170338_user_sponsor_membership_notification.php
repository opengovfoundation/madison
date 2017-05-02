<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserSponsorMembershipNotification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('notification_preferences')
            ->where('event', 'madison.sponsor.member-added')
            ->update(['event' => 'madison.user.sponsor_membership_changed'])
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
            ->where('event', 'madison.user.sponsor_membership_changed')
            ->update(['event' => 'madison.sponsor.member-added'])
            ;
    }
}

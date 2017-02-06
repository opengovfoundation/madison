<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EnableSupportVoteChangedNotification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $users = DB::table('users')->select('id')->get();
        foreach ($users as $user) {
            DB::table('notification_preferences')
                ->insert([
                    'event' => 'madison.document.support_vote_changed',
                    'type' => 'email',
                    'user_id' => $user->id,
                ])
                ;
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('notification_preferences')
            ->where('event', 'madison.document.support_vote_changed')
            ->delete()
            ;
    }
}

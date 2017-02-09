<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EnableFlaggedCommentNotification extends Migration
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
                    'event' => 'madison.comment.flagged',
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
            ->where('event', 'madison.comment.flagged')
            ->delete()
            ;
    }
}

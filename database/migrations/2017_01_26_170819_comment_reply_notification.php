<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CommentReplyNotification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('notification_preferences')
            ->where('event', 'madison.comment.created')
            ->update(['event' => 'madison.comment.replied'])
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
            ->where('event', 'madison.comment.replied')
            ->update(['event' => 'madison.comment.created'])
            ;
    }
}

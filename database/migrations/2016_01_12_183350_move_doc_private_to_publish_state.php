<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MoveDocPrivateToPublishState extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('docs', function($table) {
            $table->integer('publish_state_id')->unsigned()->nullable();
            $table->foreign('publish_state_id')->references('id')->on('publish_states');
        });

        $published_state = DB::select("select id from publish_states where value = 'published'");
        $private_state = DB::select("select id from publish_states where value = 'private'");

        DB::update('update docs set publish_state_id = ' . $published_state[0]->id . ' where private = 0');
        DB::update('update docs set publish_state_id = ' . $private_state[0]->id . ' where private = 1');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('docs', function($table) {
            $table->dropForeign('docs_publish_state_id_foreign');
            $table->dropColumn('publish_state_id');
        });
    }
}

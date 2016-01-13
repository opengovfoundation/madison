<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemovePrivateFromDocs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('docs', function($table) {
            $table->dropColumn('private');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('docs', function($table) {
            $table->boolean('private')->default(0);
            $table->index('private');
        });

        $docs = DB::select('select id, publish_state_id from docs');
        $published_state = DB::select("select id from publish_states where value = 'published'");

        foreach ($docs as $doc) {
            // Since private is just a boolean, treat published as "not
            // private", and anything marked as private so nothing is exposed
            // that shouldn't be.
            if ($doc->publish_state_id != $published_state[0]->id) {
                DB::update('update docs set private = 1 where id = ' . $doc->id);
            }
        }
    }
}

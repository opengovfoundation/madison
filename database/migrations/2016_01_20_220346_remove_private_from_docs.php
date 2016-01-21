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
        });

        // TODO: Find a way to recover non-published states. Write to file?
        DB::update("update docs set private = 0 where publish_state = 'published'");
        DB::update("update docs set private = 1 where publish_state != 'published'");
    }
}

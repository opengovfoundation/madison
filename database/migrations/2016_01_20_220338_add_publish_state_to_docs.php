<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPublishStateToDocs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('docs', function($table) {
            $table->string('publish_state')->default('unpublished');
        });

        DB::update("update docs set publish_state = 'published' where private = 0");
        DB::update("update docs set publish_state = 'private' where private = 1");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('docs', function($table) {
            $table->dropColumn('publish_state');
        });
    }
}

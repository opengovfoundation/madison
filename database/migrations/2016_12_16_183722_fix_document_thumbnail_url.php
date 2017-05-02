<?php

use Illuminate\Database\Migrations\Migration;

class FixDocumentThumbnailUrl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::update('update docs set thumbnail = replace(thumbnail, "/api/docs/", "/documents/") where thumbnail is not null');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::update('update docs set thumbnail = replace(thumbnail, "/documents/", "/api/docs/") where thumbnail is not null');
    }
}

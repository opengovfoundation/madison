<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDocTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE doc_types CHANGE style style TEXT COLLATE utf8_unicode_ci NOT NULL;');
        DB::statement('ALTER TABLE doc_types CHANGE context context TEXT COLLATE utf8_unicode_ci NOT NULL;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE doc_types CHANGE context context varchar(255) COLLATE utf8_unicode_ci NOT NULL;');
        DB::statement('ALTER TABLE doc_types CHANGE style style varchar(255) COLLATE utf8_unicode_ci NOT NULL;');
    }
}

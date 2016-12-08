<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterNoteMetaTable extends Migration
{
    public function up()
    {
        Schema::table('note_meta', function ($table) {
            $table->dropColumn('note_id');
            $table->integer('annotation_id')->unsigned();
        });
    }

    public function down()
    {
        Schema::table('note_meta', function ($table) {
            $table->string('note_id');
        });
    }
}

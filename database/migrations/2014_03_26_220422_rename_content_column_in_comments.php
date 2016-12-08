<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameContentColumnInComments extends Migration
{
    public function up()
    {
        Schema::table('comments', function ($table) {
            $table->renameColumn('content', 'text');
        });
    }

    public function down()
    {
        Schema::table('comments', function ($table) {
            $table->renameColumn('text', 'content');
        });
    }
}

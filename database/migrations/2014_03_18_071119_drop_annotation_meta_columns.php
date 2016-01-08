<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropAnnotationMetaColumns extends Migration
{
    public function up()
    {
        Schema::table('annotations', function ($table) {
            $table->dropColumn('likes');
            $table->dropColumn('dislikes');
            $table->dropColumn('flags');
        });
    }

    public function down()
    {
        Schema::table('annotations', function ($table) {
            $table->integer('likes');
            $table->integer('dislikes');
            $table->integer('flags');
        });
    }

}

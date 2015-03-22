<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSeenColumns extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comments', function ($table) {
            $table->tinyInteger('seen')->after('text')->default(0);
        });
        Schema::table('annotations', function ($table) {
            $table->tinyInteger('seen')->after('uri')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comments', function ($table) {
            $table->dropColumn('seen');
        });
        Schema::table('annotations', function ($table) {
            $table->dropColumn('seen');
        });
    }
}

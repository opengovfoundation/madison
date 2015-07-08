<?php

use Illuminate\Database\Migrations\Migration;

class AddThumbnailToDocument extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('docs', function ($table) {
            $table->text('thumbnail')->after('shortname')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('docs', function ($table) {
            $table->dropColumn('thumbnail');
        });
    }
}

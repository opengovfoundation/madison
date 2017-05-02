<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PrivateFlag extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('docs', function ($table) {
            $table->boolean('private')->default(0);
            $table->index('private');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('docs', function ($table) {
            // The name of the index is different than what we create it as.
            $table->dropIndex('docs_private_index');
            $table->dropColumn('private');
        });
    }
}

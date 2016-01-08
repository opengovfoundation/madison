<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DocTemplateFlag extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('docs', function ($table) {
            $table->boolean('is_template')->default(0);
            $table->index('is_template');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('docs', function ($table) {
            // The name of the index is different than what we create it as.
            $table->dropIndex('docs_is_template_index');
            $table->dropColumn('is_template');
        });
    }
}

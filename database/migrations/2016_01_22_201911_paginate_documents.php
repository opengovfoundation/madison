<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PaginateDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('doc_contents', function(Blueprint $table)
        {
            // This next line isn't working, it's being ignored.
            // $table->mediumtext('content')->change();
            $table->integer('page')->default(1);
            $table->index('page');
        });

        // Run a raw query since the change command above won't work.
        DB::statement('ALTER TABLE doc_contents CHANGE content content MEDIUMTEXT COLLATE utf8_unicode_ci NOT NULL;');

        Schema::table('annotations', function(Blueprint $table)
        {
          $table->integer('page')->default(1);
          $table->index('page');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('doc_contents', function(Blueprint $table)
        {
            $table->dropIndex('doc_contents_page_index');
            $table->dropColumn('page');
            // This next line isn't working, it's being ignored.
            // $table->text('content')->change();
        });

        // Run a raw query since the change command above won't work.
        DB::statement('ALTER TABLE doc_contents CHANGE content content TEXT COLLATE utf8_unicode_ci NOT NULL;');

        Schema::table('annotations', function(Blueprint $table)
        {
            $table->dropIndex('annotations_page_index');
            $table->dropColumn('page');
        });
    }
}

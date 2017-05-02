<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFulltextIdxsForDocSearch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE docs ADD FULLTEXT INDEX title_idx (title)');
        DB::statement('ALTER TABLE doc_contents ADD FULLTEXT INDEX content_idx (content)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE docs DROP INDEX title_idx');
        DB::statement('ALTER TABLE doc_contents DROP INDEX content_idx');
    }
}

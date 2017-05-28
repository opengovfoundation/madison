<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFulltextIdxsForUserSearch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE users ADD FULLTEXT INDEX fname_idx (fname)');
        DB::statement('ALTER TABLE users ADD FULLTEXT INDEX lname_idx (lname)');
        DB::statement('ALTER TABLE users ADD FULLTEXT INDEX email_idx (email)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE users DROP INDEX fname_idx');
        DB::statement('ALTER TABLE users DROP INDEX lname_idx');
        DB::statement('ALTER TABLE users DROP INDEX email_idx');
    }
}

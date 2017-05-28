<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFulltextIdxsForSponsorSearch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE sponsors ADD FULLTEXT INDEX name_idx (name)');
        DB::statement('ALTER TABLE sponsors ADD FULLTEXT INDEX address1_idx (address1)');
        DB::statement('ALTER TABLE sponsors ADD FULLTEXT INDEX address2_idx (address2)');
        DB::statement('ALTER TABLE sponsors ADD FULLTEXT INDEX city_idx (city)');
        DB::statement('ALTER TABLE sponsors ADD FULLTEXT INDEX state_idx (state)');
        DB::statement('ALTER TABLE sponsors ADD FULLTEXT INDEX postal_code_idx (postal_code)');
        DB::statement('ALTER TABLE sponsors ADD FULLTEXT INDEX phone_idx (phone)');
        DB::statement('ALTER TABLE sponsors ADD FULLTEXT INDEX display_name_idx (display_name)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE sponsors DROP INDEX name_idx');
        DB::statement('ALTER TABLE sponsors DROP INDEX address1_idx');
        DB::statement('ALTER TABLE sponsors DROP INDEX address2_idx');
        DB::statement('ALTER TABLE sponsors DROP INDEX city_idx');
        DB::statement('ALTER TABLE sponsors DROP INDEX state_idx');
        DB::statement('ALTER TABLE sponsors DROP INDEX postal_code_idx');
        DB::statement('ALTER TABLE sponsors DROP INDEX phone_idx');
        DB::statement('ALTER TABLE sponsors DROP INDEX display_name_idx');
    }
}

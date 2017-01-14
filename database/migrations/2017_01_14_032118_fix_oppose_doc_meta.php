<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixOpposeDocMeta extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::update("update doc_meta set meta_value = 0 where meta_key = 'support' and meta_value = ''");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::update("update doc_meta set meta_value = '' where meta_key = 'support' and meta_value = 0");
    }
}

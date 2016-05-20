<?php

use Illuminate\Database\Migrations\Migration;

class AddDocMetaKey extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('doc_meta', function ($table) {
            $table->unique(array('doc_id', 'user_id', 'meta_key'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('doc_meta', function ($table) {
            $table->dropUnique('doc_meta_doc_id_user_id_meta_key_unique');
        });
    }
}

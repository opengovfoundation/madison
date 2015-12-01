<?php

use Illuminate\Database\Migrations\Migration;

class AddUserToDocMeta extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('doc_meta', function ($table) {
            $table->integer('user_id')->after('doc_id')->unsigned()->nullable();

            $table->foreign('user_id')->references('id')->on('users')->on_delete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('doc_meta', function ($table) {
            $table->dropForeign('user_id_foreign');

            $table->dropColumn('user_id');
        });
    }
}

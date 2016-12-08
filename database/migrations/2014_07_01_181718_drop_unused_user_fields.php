<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropUnusedUserFields extends Migration
{
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->dropForeign('users_org_id_foreign');
            $table->dropColumn('org_id');
            $table->dropColumn('position');
            $table->dropColumn('location');
            $table->dropColumn('likes');
            $table->dropColumn('dislikes');
            $table->dropColumn('flags');
        });
    }

    public function down()
    {
        Schema::table('users', function ($table) {
            $table->integer('org_id')->unsigned()->nullable();
            $table->string('position')->nullable();
            $table->string('location')->nullable();
            $table->text('likes')->nullable();
            $table->text('dislikes')->nullable();
            $table->text('flags')->nullable();

            $table->foreign('org_id')->references('id')->on('organizations')->on_delete('set null');
        });
    }
}

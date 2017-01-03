<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateIndexesOnSponsorMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sponsor_members', function ($table) {

            $table->dropForeign('group_members_user_id_foreign');
            $table->dropForeign('group_members_group_id_foreign');

            $table->dropIndex('group_members_user_id_foreign');
            $table->dropIndex('group_members_group_id_foreign');

            // rebuild foreign key for user_id
            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');

            // rebuild foreign key for sponsor_id
            $table->foreign('sponsor_id')->references('id')->on('sponsors')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->index('user_id');
            $table->index('sponsor_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

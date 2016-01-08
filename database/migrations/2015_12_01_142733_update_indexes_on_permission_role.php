<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateIndexesOnPermissionRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('permission_role', function ($table) {
            $table->dropForeign('permission_role_permission_id_foreign');
            $table->dropForeign('permission_role_role_id_foreign');

            $table->unique('id');
            $table->dropPrimary('PRIMARY');

            $table->foreign('permission_id')->references('id')->on('permissions')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['permission_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('permission_role', function ($table) {
            // Set this to unique so we can set `id` back as primary
            $table->unique(['permission_id', 'role_id']);

            $table->dropPrimary('PRIMARY');
            $table->primary('id');

            // Put foreign keys back
            $table->dropForeign('permission_role_permission_id_foreign');
            $table->dropForeign('permission_role_role_id_foreign');

            $table->foreign('permission_id')->references('id')->on('permissions'); // assumes a users table
            $table->foreign('role_id')->references('id')->on('roles');

            // This is going to get added back in if migration is re-run,
            // so drop here
            $table->dropUnique('permission_role_id_unique');
        });
    }
}

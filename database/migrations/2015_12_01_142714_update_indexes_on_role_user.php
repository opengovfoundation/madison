<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateIndexesOnRoleUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('role_user', function ($table) {
            $table->dropForeign('assigned_roles_role_id_foreign');
            $table->dropForeign('assigned_roles_user_id_foreign');

            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')
                ->onUpdate('cascade')->onDelete('cascade');

            // Set `id` to unique constraint so we can drop it as primary key
            $table->unique('id');
            $table->dropPrimary('PRIMARY');

            // Set new primary, leave unique on `id` for safe reversal
            $table->primary(['user_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('role_user', function ($table) {
            // Set this to unique so we can set `id` back to primary key
            $table->unique(['user_id', 'role_id']);

            // Drop it as primary and set `id` back
            $table->dropPrimary('PRIMARY');
            $table->primary('id');

            $table->dropForeign('role_user_user_id_foreign');
            $table->dropForeign('role_user_role_id_foreign');

            DB::statement('ALTER TABLE `role_user` ADD CONSTRAINT `assigned_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)');
            DB::statement('ALTER TABLE `role_user` ADD CONSTRAINT `assigned_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)');

            // This is going to get added back in if migration is re-run,
            // so drop here
            $table->dropUnique('role_user_id_unique');
        });
    }
}

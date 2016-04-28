<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveDuplicatesFromAssignedRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // find duplicates in `assign_roles`
        $duplicates = DB::select('select count(*) as count, id, user_id, role_id ' .
            'from assigned_roles group by user_id, role_id having count > 1');

        foreach ($duplicates as $duplicate) {
            DB::delete(
                'delete from assigned_roles where' .
                ' role_id = ' .  $duplicate->role_id .
                ' and user_id = ' . $duplicate->user_id .
                ' and id != ' . $duplicate->id
            );
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No down should be needed, or be possible.
    }
}

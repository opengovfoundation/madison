<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Models\Role;

class CleanupOldIndependentSponsorData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Delete records in the `user_meta` table
        DB::delete('delete from user_meta where meta_key = ?', ['independent_sponsor']);

        // Get ID for INDEPENDENT_SPONSOR role
        $role = DB::selectOne('select id from roles where name = ?', ['Independent Sponsor']);

        // If there is no Independent Sponsor role go ahead and bail
        if (!$role) return;

        // Delete records in the `user_role` tabloe
        DB::delete('delete from role_user where role_id = ?', [$role->id]);

        // Delete the role
        DB::delete('delete from roles where id = ?', [$role->id]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Role::create(['name' => 'Independent Sponsor']);
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRoleNames extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $roles = DB::table('roles')->select('name', 'id')->get();

        foreach ($roles as $role) {
            $newName = str_replace('group', 'sponsor', $role->name);
            DB::update('update roles set name = ? where id = ?', [$newName, $role->id]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $roles = DB::table('roles')->select('name', 'id')->get();

        foreach ($roles as $role) {
            $newName = str_replace('sponsor', 'group', $role->name);
            DB::update('update roles set name = ? where id = ?', [$newName, $role->id]);
        }
    }
}

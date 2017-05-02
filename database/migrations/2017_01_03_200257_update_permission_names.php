<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePermissionNames extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $permissions = DB::table('permissions')->select('name', 'id')->get();

        foreach ($permissions as $permission) {
            $newName = str_replace('group', 'sponsor', $permission->name);
            DB::update('update permissions set name = ? where id = ?', [$newName, $permission->id]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $permissions = DB::table('permissions')->select('name', 'id')->get();

        foreach ($permissions as $permission) {
            $newName = str_replace('sponsor', 'group', $permission->name);
            DB::update('update permissions set name = ? where id = ?', [$newName, $permission->id]);
        }
    }
}

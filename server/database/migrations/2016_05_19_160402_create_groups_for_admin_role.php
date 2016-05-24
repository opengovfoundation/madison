<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Role;
use App\Models\Group;

class CreateGroupsForAdminRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $admin_role_id = DB::select(
            'select id from roles where name = ?',
            [Role::ROLE_ADMIN]
        )[0]->id;

        $individual_sponsors = DB::select(
            'select user_id from role_user where role_id = ?',
            [$admin_role_id]
        );

        // Bail if there are none to handle
        if (count($individual_sponsors) < 1) return;

        foreach ($individual_sponsors as $individual_sponsor) {
            $user = DB::select('select fname, lname, address1, address2, ' .
                'city, state, postal_code, phone from users where id = ?',
                [$individual_sponsor->user_id]
            )[0];

            $group = new Group([
                'name' => $user->fname . ' ' . $user->lname,
                'display_name' => $user->fname . ' ' . $user->lname,
                'user_id' => $individual_sponsor->user_id,
                'address1' => $user->address1 || ' ',
                'address2' => $user->address2 || ' ',
                'city' => $user->city || ' ',
                'state' => $user->state || ' ',
                'postal_code' => $user->postal_code || ' ',
                'phone' => $user->phone || ' ',
                'individual' => true,
                'status' => 'active'
            ]);

            $group->save();
            $group->addMember($individual_sponsor->user_id, Group::ROLE_OWNER);
        }
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

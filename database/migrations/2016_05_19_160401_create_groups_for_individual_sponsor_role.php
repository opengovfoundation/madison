<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Role;
use App\Models\Group;

class CreateGroupsForIndividualSponsorRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $ind_sponsor_roles = DB::select(
            'select id from roles where name = ?',
            [Role::ROLE_INDEPENDENT_SPONSOR]
        );

        // If migration runs before DB is seeded, this won't be found
        if (count($ind_sponsor_roles) == 0) return;

        // Get all the ID's for users with this role
        $ind_sponsor_role_id = $ind_sponsor_roles[0]->id;
        $individual_sponsors = DB::select(
            'select user_id from role_user where role_id = ?',
            [$ind_sponsor_role_id]
        );

        // Bail if there are none to handle
        if (count($individual_sponsors) < 1) return;

        foreach ($individual_sponsors as $individual_sponsor) {
            // Get the user's info
            $user = DB::select('select fname, lname, address1, address2, ' .
                'city, state, postal_code, phone from users where id = ?',
                [$individual_sponsor->user_id]
            )[0];

            // Create the individual group for this user
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

            // Save it and add the user as the owner
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
        // We won't be able to track *how* these groups were created, so
        // deleting all might be undesirable. Removing these groups will be
        // handled in a different downstream migration.
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Role;
use App\Models\Sponsor;

class CreateGroupsForDocOwningUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // We're getting all the documents that belong to a User under the old
        // independent sponsorship model and converting them to belong to those
        // users' newly made individual groups.
        //
        // The migration right before this successfully handled creating groups
        // for those with the Independent Sponsor "role", and a prior migration
        // handled it for users with the `independent_sponsor` meta value.
        //
        // This one takes care of anything left over, being careful to only make
        // groups "active" if the user had a currently enabled permission level.

        $doc_user_records = DB::select('select user_id from doc_user');
        if (count($doc_user_records) == 0) return;

        foreach($doc_user_records as $record) {
            // Check if the user has an individual group already
            $individual_group = Sponsor::where('user_id', $record->user_id)->first();

            // If there's already an individual group, skip it
            if ($individual_group == null) {

                // Check if user is currently independent sponsor or admin
                $has_admin_role = DB::selectOne(
                    'select * from role_user where user_id = ? and role_id = ?',
                    [$record->user_id, Role::ROLE_ADMIN]
                );
                $has_ind_sponsor_role = DB::selectOne(
                    'select * from role_user where user_id = ? and role_id = ?',
                    [$record->user_id, Role::ROLE_INDEPENDENT_SPONSOR]
                );
                $has_ind_sponsor_meta = DB::selectOne(
                    'select * from user_meta where user_id = ? and meta_key = ? and meta_value = ?',
                    [$record->user_id, 'independent_sponsor', 1]
                );

                $status = 'pending';

                // Only make group active if they were active ind sponsor or
                // admin before.
                //
                // The "pending" group will still allow access to existing
                // documents, but will prevent new ones from being created until
                // the group is verified.
                //
                // If they are already verified as admin or sponsor, the group
                // will be active.
                if ($has_admin_role || $has_ind_sponsor_role || $has_ind_sponsor_meta) {
                    $status = 'active';
                }

                // Create the group
                $individual_group = Sponsor::createIndividualSponsor($record->user_id, [
                    'status' => $status
                ]);
            }
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

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Models\Group;
use App\Models\UserMeta;

class PopulateIndividualSponsorsAsGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $individual_sponsors = DB::select("select user_id, meta_value from " .
            "user_meta where meta_key = 'independent_sponsor'");

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
            ]);

            switch ($individual_sponsor->meta_value) {
                case 1: $group->status = 'active'; break;
                case 0: $group->status = 'pending'; break;
            }

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
        $individual_groups = DB::select(
            'select user_id, status from groups where individual = 1'
        );

        foreach ($individual_groups as $individual_group) {
            $existing_meta_records = DB::select(
                'select meta_value from user_meta ' .
                'where user_id = ? and meta_key = ?',
                [
                    $individual_group->user_id,
                    'independent_sponsor'
                ]
            );

            if (count($existing_meta_records) === 0) {
                // no record, create a new one
                $meta_record = new UserMeta([
                    'user_id' => $individual_group->user_id,
                    'meta_key' => UserMeta::TYPE_INDEPENDENT_SPONSOR,
                    'meta_value' => $individual_group->status === 'active' ? 1 : 0
                ]);
            } else {
                // record exists! update status if needed
                $group_status = $individual_group->status === 'active' ? 1 : 0;

                if ($group_status !== $existing_meta_records[0]->meta_value) {
                    DB::update(
                        'update user_meta set meta_value = ? ' .
                        'where user_id = ? and meta_key = ?',
                        [
                            $group_status,
                            $individual_group->user_id,
                            'independent_sponsor'
                        ]
                    );
                }
            }
        }

        DB::delete('delete from groups where individual = 1');
    }
}

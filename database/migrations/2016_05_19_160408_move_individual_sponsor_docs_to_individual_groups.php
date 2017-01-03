<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\User;
use App\Models\Sponsor;

class MoveIndividualSponsorDocsToIndividualGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $user_doc_records = DB::select('select doc_id, user_id from doc_user');

        foreach ($user_doc_records as $record) {
            $ind_groups = DB::select(
                'select id from groups where user_id = ?',
                [$record->user_id]
            );

            if (count($ind_groups) == 0) {
                continue;
            }

            $group_id = $ind_groups[0]->id;

            DB::table('doc_group')->insert([
                'doc_id' => $record->doc_id,
                'group_id' => $group_id
            ]);
        }

        Schema::dropIfExists('doc_user');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('doc_user')) {
            Schema::create('doc_user', function ($table) {
                $table->integer('doc_id')->unsigned();
                $table->integer('user_id')->unsigned();

                $table->foreign('doc_id')->references('id')->on('docs')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // get individual groups
        $individual_groups = Sponsor::where('individual', true);

        foreach ($individual_groups as $group) {
            // find docs belonging to those groups
            $doc_ids = DB::select(
                'doc_id from doc_group where group_id = ?',
                [$group->id]
            );

            // find user id, should be only group member
            $user = $group->members()->first();

            foreach ($doc_ids as $doc_id) {
                // make entry in `doc_user` for each
                DB::table('doc_user')->insert([
                    'user_id' => $user->id(),
                    'doc_id' => $doc_id
                ]);

                // remove from `doc_group` table
                DB::table('doc_group')->where('doc_id', $doc_id)->delete();
            }
        }
    }
}

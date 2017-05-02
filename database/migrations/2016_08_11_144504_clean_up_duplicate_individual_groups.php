<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CleanUpDuplicateIndividualGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Get unique collection of individual group names
        // Using DB::table here because we haven't switched to the `sponsor`
        // model name yet in this migration
        $individual_groups = DB::table('groups')->select('name', 'id')->where('individual', 1)->orderBy('id', 'ASC')->groupBy('name')->get();

        foreach ($individual_groups as $main_group) {
            // Check if another individual group exists under same name
            $duplicate_groups = DB::table('groups')->where('name', $main_group['name'])
                ->where('id', '!=', $main_group['id'])
                ->where('individual', 1)
                ->get();

            if (count($duplicate_groups) > 0) {
                foreach ($duplicate_groups as $duplicate_group) {
                    // Update any potential docs to belong to first group
                    DB::raw('update doc_group set group_id = ? where group_id = ?', [$main_group['id'], $duplicate_group['id']]);
                    // delete higher id group
                    DB::delete('delete from groups where id = ?', [$duplicate_group['id']]);
                }
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
        // No turning back! (Shouldn't need to anyways)
    }
}

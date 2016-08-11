<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Group;

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
        $individual_groups = Group::select('name', 'id')->where('individual', 1)->orderBy('id', 'ASC')->groupBy('name')->get();

        foreach ($individual_groups as $group) {
            // Check if another individual group exists under same name
            $duplicate_group = Group::where('name', $group['name'])
                ->where('id', '!=', $group['id'])
                ->where('individual', 1)
                ->first();

            if ($duplicate_group) {
                // Update any potential docs to belong to first group
                DB::raw('update doc_group set group_id = ? where group_id = ?', [$group['id'], $duplicate_group['id']]);
                // delete higher id group
                DB::delete('delete from groups where id = ?', [$duplicate_group['id']]);
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

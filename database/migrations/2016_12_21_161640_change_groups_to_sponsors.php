<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeGroupsToSponsors extends Migration
{

    public function __construct()
    {
        DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('groups', 'sponsors');
        Schema::rename('group_members', 'sponsor_members');
        Schema::rename('doc_group', 'doc_sponsor');

        Schema::table('sponsor_members', function ($table) {
            $table->renameColumn('group_id', 'sponsor_id');
        });

        Schema::table('doc_sponsor', function ($table) {
            $table->renameColumn('group_id', 'sponsor_id');
        });

        Schema::table('notification_preferences', function ($table) {
            $table->renameColumn('group_id', 'sponsor_id');
        });
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

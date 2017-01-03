<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateIndexesOnDocSponsorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('doc_sponsor', function ($table) {

            $table->dropForeign('doc_group_doc_id_foreign');
            $table->dropForeign('doc_group_group_id_foreign');

            $table->dropIndex('doc_group_doc_id_foreign');
            $table->dropIndex('doc_group_group_id_foreign');

            // rebuild foreign key for doc_id
            $table->foreign('doc_id')->references('id')->on('docs')
                ->onUpdate('cascade')->onDelete('cascade');

            // rebuild foreign key for sponsor_id
            $table->foreign('sponsor_id')->references('id')->on('sponsors')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->index('doc_id');
            $table->index('sponsor_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // no going back
    }
}

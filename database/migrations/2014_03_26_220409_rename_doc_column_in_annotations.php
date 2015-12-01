<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameDocColumnInAnnotations extends Migration
{
    public function up()
    {
        Schema::table('annotations', function ($table) {
            $table->dropForeign('annotations_doc_foreign');
            $table->renameColumn('doc', 'doc_id');

            $table->foreign('doc_id')->references('id')->on('docs')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('annotations', function ($table) {
            $table->dropForeign('annotations_doc_id_foreign');
            $table->renameColumn('doc_id', 'doc');

            $table->foreign('doc')->references('id')->on('docs');
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;

class AddSoftDeletes extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('annotations', function ($table) {
            $table->softDeletes();
        });

        Schema::table('annotation_comments', function ($table) {
            $table->softDeletes();
        });

        Schema::table('annotation_permissions', function ($table) {
            $table->softDeletes();
        });

        Schema::table('annotation_ranges', function ($table) {
            $table->softDeletes();
        });

        Schema::table('annotation_tags', function ($table) {
            $table->softDeletes();
        });

        Schema::table('comments', function ($table) {
            $table->softDeletes();
        });

        Schema::table('docs', function ($table) {
            $table->softDeletes();
        });

        Schema::table('doc_contents', function ($table) {
            $table->softDeletes();
        });

        Schema::table('doc_meta', function ($table) {
            $table->softDeletes();
        });

        Schema::table('organizations', function ($table) {
            $table->softDeletes();
        });

        Schema::table('users', function ($table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('annotations', function ($table) {
            $table->dropColumn('deleted_at');
        });

        Schema::table('annotation_comments', function ($table) {
            $table->dropColumn('deleted_at');
        });

        Schema::table('annotation_permissions', function ($table) {
            $table->dropColumn('deleted_at');
        });

        Schema::table('annotation_ranges', function ($table) {
            $table->dropColumn('deleted_at');
        });

        Schema::table('annotation_tags', function ($table) {
            $table->dropColumn('deleted_at');
        });

        Schema::table('comments', function ($table) {
            $table->dropColumn('deleted_at');
        });

        Schema::table('docs', function ($table) {
            $table->dropColumn('deleted_at');
        });

        Schema::table('doc_contents', function ($table) {
            $table->dropColumn('deleted_at');
        });

        Schema::table('doc_meta', function ($table) {
            $table->dropColumn('deleted_at');
        });

        Schema::table('organizations', function ($table) {
            $table->dropColumn('deleted_at');
        });

        Schema::table('users', function ($table) {
            $table->dropColumn('deleted_at');
        });
    }
}

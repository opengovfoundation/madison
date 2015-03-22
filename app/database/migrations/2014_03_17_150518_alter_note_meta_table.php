<?php

class AlterNoteMetaTable extends DualMigration
{

    public function upMySQL()
    {
        Schema::table('note_meta', function ($table) {
            $table->dropColumn('note_id');
            $table->integer('annotation_id')->unsigned();
        });
    }

    public function downMySQL()
    {
        Schema::table('note_meta', function ($table) {
            $table->string('note_id');
        });
    }

    public function upSQLite()
    {
        //Create temporary table
        Schema::create('note_meta_temp', function ($table) {
            $table->engine = "InnoDB";
            $table->increments('id');
            $table->integer('annotation_id')->unsigned();
            $table->integer('user_id')->unsigned()->nullable();
            $table->string('meta_key');
            $table->string('meta_value')->nullable();
            $table->timestamps();
            
            //Set foreign keys
            $table->foreign('user_id')->references('id')->on('users')->on_delete('cascade');
        });

        //Copy any existing data
        DB::raw('INSERT INTO `note_meta_temp` (`id`, `annotation_id`, `user_id`, `meta_key`, `meta_value`, `created_at`, `updated_at`) SELECT `id`, `note_id`, `user_id`, `meta_key`, `meta_value`, `created_at`, `updated_at` FROM `note_meta`');

        //Drop old table
        Schema::drop('note_meta');

        //Rename temp table
        Schema::rename('note_meta_temp', 'note_meta');
    }

    public function downSQLite()
    {
        Schema::create('note_meta_temp', function ($table) {
            $table->engine = "InnoDB";
            $table->increments('id');
            $table->string('note_id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->string('meta_key');
            $table->string('meta_value')->nullable();
            $table->timestamps();
            
            //Set foreign keys
            $table->foreign('user_id')->references('id')->on('users')->on_delete('cascade');
        });

        //Copy any existing data
        DB::raw('INSERT INTO `note_meta_temp` (`id`, `note_id`, `user_id`, `meta_key`, `meta_value`, `created_at`, `updated_at`) SELECT `id`, `annotation_id`, `user_id`, `meta_key`, `meta_value`, `created_at`, `updated_at` FROM `note_meta`');


        Schema::drop('note_meta');
        Schema::rename('note_meta_temp', 'note_meta');
    }
}

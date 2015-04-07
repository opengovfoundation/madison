<?php

class MakeAnnotationsSearchIdNullable extends DualMigration
{
    public function upMySQL()
    {
        DB::statement('ALTER TABLE `annotations` CHANGE COLUMN `search_id` `search_id` VARCHAR(255) NULL');
    }

    public function downMySQL()
    {
        DB::statement('ALTER TABLE `annotations` CHANGE COLUMN `search_id` VARCHAR(255) IS NOT NULL');
    }

    public function upSQLite()
    {
        DB::statement('PRAGMA foreign_keys = OFF');

        Schema::create('annotations_temp', function ($table) {
            $table->engine = "InnoDB";

            $table->increments('id');
            $table->string('search_id')->nullable();
            $table->integer('user_id')->unsigned();
            $table->integer('doc')->unsigned();
            $table->string('quote');
            $table->string('text');
            $table->string('uri');
            $table->timestamps();

            // $table->foreign('user_id')->references('id')->on('users');
            // $table->foreign('doc')->references('id')->on('docs');
            // $table->unique('search_id');
        });

        DB::raw('INSERT INTO `annotations_temp` (`id`, `search_id`, `user_id`, `doc`, `quote`, `text`, `uri`, `created_at`, `updated_at`, `deleted_at`) SELECT `id`, `search_id`, `user_id`, `doc`, `quote`, `text`, `uri`, `created_at`, `updated_at`, `deleted_at` FROM `annotations`');

        Schema::drop('annotations');
        Schema::rename('annotations_temp', 'annotations');

        DB::statement('PRAGMA foreign_keys = ON');
    }

    public function downSQLite()
    {
        DB::statement('PRAGMA foreign_keys = OFF');

        Schema::create('annotations_temp', function ($table) {
            $table->engine = "InnoDB";

            $table->increments('id');
            $table->string('search_id');
            $table->integer('user_id')->unsigned();
            $table->integer('doc')->unsigned();
            $table->string('quote');
            $table->string('text');
            $table->string('uri');
            $table->timestamps();

            // $table->foreign('user_id')->references('id')->on('users');
            // $table->foreign('doc')->references('id')->on('docs');
            // $table->unique('search_id');
        });

        DB::raw('INSERT INTO `annotations_temp` (`id`, `search_id`, `user_id`, `doc`, `quote`, `text`, `uri`, `created_at`, `updated_at`, `deleted_at`) SELECT `id`, `search_id`, `user_id`, `doc`, `quote`, `text`, `uri`, `created_at`, `updated_at`, `deleted_at` FROM `annotations`');

        Schema::drop('annotations');
        Schema::rename('annotations_temp', 'annotations');

        DB::statement('PRAGMA foreign_keys = ON');
    }
}

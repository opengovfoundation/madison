<?php

class AlterAnnotationsIncreaseFieldLength extends DualMigration
{
    public function upMySQL()
    {
        DB::statement('ALTER TABLE `annotations` MODIFY COLUMN `quote` TEXT');
        DB::statement('ALTER TABLE `annotations` MODIFY COLUMN `text` TEXT');
    }

    public function downMySQL()
    {
        DB::statement('ALTER TABLE `annotations` MODIFY COLUMN `quote` VARCHAR(255)');
        DB::statement('ALTER TABLE `annotations` MODIFY COLUMN `text` VARCHAR(255)');
    }

    public function upSQLite()
    {
        DB::statement('PRAGMA foreign_keys = OFF');

        Schema::create('annotations_temp', function ($table) {
            $table->engine = "InnoDB";

            $table->increments('id');
            $table->string('search_id')->nullable();
            $table->integer('user_id')->unsigned();
            $table->integer('doc_id')->unsigned();
            $table->text('quote');
            $table->text('text');
            $table->string('uri');
            $table->timestamps();

            // $table->foreign('user_id')->references('id')->on('users');
            // $table->foreign('doc_id')->references('id')->on('docs');
            // $table->unique('search_id');

        });

        DB::statement('INSERT INTO `annotations_temp` (`id`, `search_id`, `user_id`, `doc_id`, `quote`, `text`, `uri`, `created_at`, `updated_at`) SELECT `id`, `search_id`, `user_id`, `doc_id`, `quote`, `text`, `uri`, `created_at`, `updated_at` FROM `annotations`');

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
            $table->string('search_id')->nullable();
            $table->integer('user_id')->unsigned();
            $table->integer('doc_id')->unsigned();
            $table->string('quote');
            $table->string('text');
            $table->string('uri');
            $table->timestamps();

            // $table->foreign('user_id')->references('id')->on('users');
            // $table->foreign('doc_id')->references('id')->on('docs');
            // $table->unique('search_id');

        });

        DB::statement('INSERT INTO `annotations_temp` (`id`, `search_id`, `user_id`, `doc_id`, `quote`, `text`, `uri`, `created_at`, `updated_at`) SELECT `id`, `search_id`, `user_id`, `doc_id`, `quote`, `text`, `uri`, `created_at`, `updated_at` FROM `annotations`');

        Schema::drop('annotations');
        Schema::rename('annotations_temp', 'annotations');

        DB::statement('PRAGMA foreign_keys = ON');
    }
}

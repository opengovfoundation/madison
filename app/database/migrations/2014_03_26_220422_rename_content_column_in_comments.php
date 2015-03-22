<?php

class RenameContentColumnInComments extends DualMigration
{

    public function upMySQL()
    {
        Schema::table('comments', function ($table) {
            $table->renameColumn('content', 'text');
        });
    }

    public function downMySQL()
    {
        Schema::table('comments', function ($table) {
            $table->renameColumn('text', 'content');
        });
    }

    public function upSQLite()
    {
        DB::statement('PRAGMA foreign_keys = OFF');

        Schema::create('comments_temp', function ($table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('doc_id')->unsigned();
            $table->integer('parent_id')->unsigned()->nullable();
            $table->text('text');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->on_delete('cascade');
            $table->foreign('doc_id')->references('id')->on('docs')->on_delete('cascade');
            $table->foreign('parent_id')->references('id')->on('comments')->onDelete('cascade');
        });

        DB::statement('INSERT INTO `comments_temp` (`id`, `user_id`, `doc_id`, `parent_id`, `text`, `created_at`, `updated_at`) SELECT `id`, `user_id`, `doc_id`, `parent_id`, `content`, `created_at`, `updated_at` FROM comments');

        Schema::drop('comments');
        Schema::rename('comments_temp', 'comments');

        DB::statement('PRAGMA foreign_keys = ON');
    }

    public function downSQLite()
    {
        DB::statement('PRAGMA foreign_keys = OFF');

        Schema::create('comments_temp', function ($table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('doc_id')->unsigned();
            $table->integer('parent_id')->unsigned()->nullable();
            $table->text('content');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->on_delete('cascade');
            $table->foreign('doc_id')->references('id')->on('docs')->on_delete('cascade');
            $table->foreign('parent_id')->references('id')->on('comments')->onDelete('cascade');
        });

        DB::statement('INSERT INTO `comments_temp` (`id`, `user_id`, `doc_id`, `parent_id`, `content`, `created_at`, `updated_at`) SELECT `id`, `user_id`, `doc_id`, `parent_id`, `text`, `created_at`, `updated_at` FROM comments');

        Schema::drop('comments');
        Schema::rename('comments_temp', 'comments');

        DB::statement('PRAGMA foreign_keys = ON');
    }
}

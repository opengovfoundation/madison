<?php


class DropUnusedUserFields extends DualMigration
{
    public function upMySQL()
    {
        Schema::table('users', function ($table) {
            $table->dropForeign('users_org_id_foreign');
            $table->dropColumn('org_id');
            $table->dropColumn('position');
            $table->dropColumn('location');
            $table->dropColumn('likes');
            $table->dropColumn('dislikes');
            $table->dropColumn('flags');
        });
    }

    public function downMySQL()
    {
        Schema::table('users', function ($table) {
            $table->integer('org_id')->unsigned()->nullable();
            $table->string('position')->nullable();
            $table->string('location')->nullable();
            $table->text('likes')->nullable();
            $table->text('dislikes')->nullable();
            $table->text('flags')->nullable();

            $table->foreign('org_id')->references('id')->on('organizations')->on_delete('set null');
        });
    }

    public function upSQLite()
    {
        DB::statement('PRAGMA foreign_keys = OFF');

        Schema::create('users_temp', function ($table) {
            $table->engine = "InnoDB";
            $table->increments('id');
            $table->string('email')->unique();
            $table->string('password', 100);
            $table->string('fname');
            $table->string('lname');
            $table->string('phone')->nullable();
            $table->string('url')->nullable();
            $table->string('token', 25);
            $table->timestamp('last_login')->nullable();
            $table->timestamps();
        });

        DB::statement('INSERT INTO `users_temp` (`id`, `email`, `password`, `fname`, `lname`, `phone`, `url`, `last_login`, `created_at`, `updated_at`) SELECT `id`, `email`, `password`, `fname`, `lname`, `phone`, `url`, `last_login`, `created_at`, `updated_at` FROM `users`');

        Schema::drop('users');
        Schema::rename('users_temp', 'users');

        DB::statement('PRAGMA foreign_keys = ON');
    }

    public function downSQLite()
    {
        DB::statement('PRAGMA foreign_keys = OFF');

        Schema::create('users_temp', function ($table) {
            $table->engine = "InnoDB";
            $table->increments('id');
            $table->string('email')->unique();
            $table->string('password', 100);
            $table->string('fname');
            $table->string('lname');
            $table->string('phone')->nullable();
            $table->integer('org_id')->unsigned()->nullable();
            $table->string('position')->nullable();
            $table->string('location')->nullable();
            $table->string('url')->nullable();
            $table->string('token', 25);
            $table->text('likes')->nullable();
            $table->text('dislikes')->nullable();
            $table->text('flags')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->timestamps();

            //Set foreign keys
            $table->foreign('org_id')->references('id')->on('organizations')->on_delete('set null');
        });

        DB::statement('INSERT INTO `users_temp` (`id`, `email`, `password`, `fname`, `lname`, `phone`, `org_id`, `position`, `location`, `url`, `token`, `likes`, `dislikes`, `flags`, `last_login`, `created_at`, `updated_at`) SELECT `id`, `email`, `password`, `fname`, `lname`, `phone`, `org_id`, `position`, `location`, `url`, `token`, `likes`, `dislikes`, `flags`, `last_login`, `created_at`, `updated_at` FROM `users`');

        Schema::drop('users');
        Schema::rename('users_temp', 'users');

        DB::statement('PRAGMA foreign_keys = ON');
    }
}

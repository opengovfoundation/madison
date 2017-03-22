<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Tables to truncate.
     *
     * @var array
     */
    private $tables = [
        'annotation_permissions',
        'annotation_types_comment',
        'annotation_types_flag',
        'annotation_types_like',
        'annotation_types_range',
        'annotation_types_seen',
        'annotation_types_tag',
        'annotations',
        'categories',
        'category_doc',
        'configs',
        'doc_contents',
        'doc_meta',
        'doc_sponsor',
        'doc_types',
        'docs',
        'failed_jobs',
        'jobs',
        'notification_preferences',
        'page_contents',
        'pages',
        'password_reminders',
        'password_resets',
        'permission_role',
        'permissions',
        'role_user',
        'roles',
        'sessions',
        'settings',
        'sponsor_members',
        'sponsors',
        'user_meta',
        'users',
    ];

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->cleanDatabase();

        Eloquent::unguard();

        $this->call('UsersTableSeeder');
        $this->call('RbacSeeder');
        $this->call('NotificationSeeder');
        $this->call('SponsorsTableSeeder');
        $this->call('DocumentsTableSeeder');
        $this->call('CategoriesTableSeeder');
        $this->call('AnnotationsTableSeeder');
        $this->call('PagesTableSeeder');

        if (App::environment() === 'testing') {
            $this->call('TestSeeder');
        }
    }

    /**
     * Clean database tables removing foreign keys.
     */
    protected function cleanDatabase()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ($this->tables as $tableName) {
            DB::table($tableName)->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}

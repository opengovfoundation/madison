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
        'annotations',
        'categories',
        'category_doc',
        'dates',
        'doc_contents',
        'doc_group',
        'doc_meta',
        'doc_status',
        'docs',
        'group_members',
        'groups',
        'jobs',
        'notification_preferences',
        'organizations',
        'pages',
        'page_contents',
        'password_reminders',
        'permission_role',
        'permissions',
        'role_user',
        'roles',
        'settings',
        'statuses',
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
        $this->call('GroupsTableSeeder');
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

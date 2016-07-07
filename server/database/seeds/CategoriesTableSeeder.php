<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\Doc;

class CategoriesTableSeeder extends Seeder
{
    public function run()
    {
        $adminEmail = Config::get('madison.seeder.admin_email');
        $adminPassword = Config::get('madison.seeder.admin_password');

        // Login as admin to create categories
        $credentials = ['email' => $adminEmail, 'password' => $adminPassword];
        Auth::attempt($credentials);
        $admin = Auth::user();

        $doc = Doc::find(1);

        $cat1 = factory(Category::class)->create();
        $cat2 = factory(Category::class)->create();

        $doc->categories()->sync([$cat1->id, $cat2->id]);
    }
}

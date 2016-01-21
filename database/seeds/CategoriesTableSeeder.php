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

        // Login as admin to create docs
        $credentials = ['email' => $adminEmail, 'password' => $adminPassword];
        Auth::attempt($credentials);
        $admin = Auth::user();

        $category1 = ['text' => 'first category'];
        $category2 = ['text' => 'second category'];

        Input::replace($input = ['categories' => [$category1]]);
        App::make('App\Http\Controllers\DocumentApiController')->postCategories(1);

        Input::replace($input = ['categories' => [$category1, $category2]]);
        App::make('App\Http\Controllers\DocumentApiController')->postCategories(1);
    }
}

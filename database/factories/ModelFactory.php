<?php
/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

use App\Models\Annotation;
use App\Models\AnnotationTypes;
use App\Models\Category;
use App\Models\Doc as Document;
use App\Models\DocContent;
use App\Models\Page;
use App\Models\PageContent;
use App\Models\Role;
use App\Models\Sponsor;
use App\Models\User;

$factory->define(User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'fname' => $faker->firstName,
        'lname' => $faker->lastName,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = 'secret',
        'token' => str_random(10),
        'remember_token' => str_random(10),
    ];
});

$factory->defineAs(Role::class, 'admin_role', function (Faker\Generator $faker) {
    return ['name' => Role::ROLE_ADMIN];
});

$factory->define(Sponsor::class, function (Faker\Generator $faker) {
    $name = $faker->company;
    $display_name = "{$name} {$faker->companySuffix}";
    return [
        'name' => $name,
        'display_name' => $display_name,
        'address1' => $faker->streetAddress,
        'city' => $faker->city,
        'state' => $faker->state,
        'postal_code' => $faker->postcode,
        'phone' => $faker->phoneNumber
    ];
});

$factory->define(Page::class, function (Faker\Generator $faker) {
    return [ 'nav_title' => $faker->domainWord ];
});

$factory->define(PageContent::class, function (Faker\Generator $faker) {
    return [ 'content' => join(' ', $faker->sentences) ];
});

$factory->define(Document::class, function (Faker\Generator $faker) {
    return [
        'title' => substr($faker->sentence(5), 0, -1),
    ];
});

$factory->define(DocContent::class, function (Faker\Generator $faker) {
    return [
        'content' => $faker->paragraphs(100, true),
    ];
});

$factory->define(Category::class, function (Faker\Generator $faker) {
    return [ 'name' => $faker->words(2, true) ];
});

$factory->define(Annotation::class, function (Faker\Generator $faker) {
    return [
    ];
});

$factory->define(AnnotationTypes\Comment::class, function (Faker\Generator $faker) {
    return [
        'content' => $faker->paragraphs($faker->numberBetween(1, 3), true),
    ];
});

$factory->define(AnnotationTypes\Range::class, function (Faker\Generator $faker) {
    return [
        'start' => '/p[1]',
        'end' => '/p[1]',
        'start_offset' => $faker->numberBetween(1, 20),
        'end_offset' => $faker->numberBetween(1, 20),
    ];
});

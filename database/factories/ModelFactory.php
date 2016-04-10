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

$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
    return [
        'fname' => $faker->firstName,
        'lname' => $faker->lastName,
        'email' => $faker->email,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});

$factory->defineAs(App\Models\Role::class, 'admin_role', function (Faker\Generator $faker) {
    return ['name' => App\Models\Role::ROLE_ADMIN];
});

$factory->define(App\Models\Group::class, function (Faker\Generator $faker) {
    $name = $faker->company;
    $display_name = "{$name} {$faker->companySuffix}";
    return [
        'name' => $name,
        'display_name' => $display_name,
        'address1' => $faker->streetAddress,
        'city' => $faker->city,
        'state' => $faker->state,
        'postal_code' => $faker->postcode,
        'phone' => $faker->e164PhoneNumber
    ];
});

$factory->define(App\Models\Page::class, function (Faker\Generator $faker) {
    return [ 'nav_title' => $faker->domainWord ];
});

$factory->define(App\Models\PageContent::class, function (Faker\Generator $faker) {
    return [ 'content' => join(' ', $faker->sentences) ];
});

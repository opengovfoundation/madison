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
use App\Models\Doc as Document;
use App\Models\DocContent;
use App\Models\Page;
use App\Models\PageContent;
use App\Models\Role;
use App\Models\Sponsor;
use App\Models\User;

use Illuminate\Notifications\DatabaseNotification;

use Ramsey\Uuid\Uuid;

$factory->define(User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'fname' => $faker->firstName,
        'lname' => $faker->lastName,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = 'secret',
        'token' => '',
        'remember_token' => str_random(10),
    ];
});

$factory->state(User::class, 'emailUnverified', function (Faker\Generator $faker) {
    return [
        'token' => str_random(10),
    ];
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

$factory->state(Page::class, 'randomize', function (Faker\Generator $faker) {
    return [
        'url' => $faker->slug,
        'page_title' => $faker->words(2, true),
        'nav_title' => $faker->domainWord,
        'header' => $faker->words(2, true),
    ];
});

$factory->state(Page::class, 'external', function (Faker\Generator $faker) {
    return [
        'url' => $faker->url,
        'external' => true
    ];
});

$factory->define(PageContent::class, function (Faker\Generator $faker) {
    return [ 'content' => join(' ', $faker->sentences) ];
});

$factory->define(Document::class, function (Faker\Generator $faker) {
    return [ 'title' => $faker->words(5, true) ];
});

$factory->define(DocContent::class, function (Faker\Generator $faker) {
    $content = '';

    $numHeadings = rand(30, 40);
    $headings = $faker->sentences($numHeadings);

    foreach ($headings as $heading) {
        $content .= str_repeat('#', rand(1, 4)) . ' ' . $heading;
        $content .= "\n\n";
        $content .= $faker->paragraphs(rand(3, 5), true);
        $content .= "\n\n";
    }

    return [
        'content' => $content,
    ];
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

$factory->define(DatabaseNotification::class, function (Faker\Generator $faker) {
    return [
        'id' => Uuid::uuid4()->toString(),
        'notifiable_id' => 1,
        'notifiable_type' => 'App\Models\User',
        'data' => [],
    ];
});

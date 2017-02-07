<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

use App\Models\Annotation;
use App\Models\Doc as Document;
use App\Models\User;
use App\Models\Page;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

Route::bind('comment', function ($value) {
    $comment = Annotation::find($value);
    if ($comment) {
        return $comment;
    }

    $comment = Annotation::where('str_id', $value)->first();
    if ($comment) {
        return $comment;
    }

    throw new NotFoundHttpException;
});

Route::bind('document', function ($value) {
    $doc = Document::find($value);
    if ($doc) {
        return $doc;
    }

    $doc = Document::where('slug', $value)->first();
    if ($doc) {
        return $doc;
    }

    throw new NotFoundHttpException;
});

Route::bind('documentTrashed', function ($value) {
    $doc = Document::withTrashed()->find($value);
    if ($doc) {
        return $doc;
    }

    $doc = Document::withTrashed()->where('slug', $value)->first();
    if ($doc) {
        return $doc;
    }

    throw new NotFoundHttpException;
});

Route::bind('page', function ($value) {
    $page = Page::where('url', '/' . $value)
        ->orWhere('id', $value)
        ->first();

    if ($page) {
        return $page;
    }

    throw new NotFoundHttpException;
});


// Authentication
Auth::routes();


// Home page
Route::get('/', 'HomeController@index');


// Comments
Route::resource('documents.comments', 'CommentController');
Route::post('documents/{document}/comments/{comment}/comments', 'CommentController@storeReply')
    ->name('documents.comments.storeReply');
Route::post('documents/{document}/comments/{comment}/likes', 'CommentController@storeLikes')
    ->name('documents.comments.storeLikes');
Route::post('documents/{document}/comments/{comment}/flags', 'CommentController@storeFlags')
    ->name('documents.comments.storeFlags');


// Documents
Route::resource('documents', 'DocumentController');

Route::post('/documents/{document}/pages', 'DocumentController@storePage')
    ->name('documents.pages.store');

Route::get('/documents/{document}/images/{image}', 'DocumentController@showImage')
    ->name('documents.images.show');

Route::delete('/documents/{document}/images/{image}', 'DocumentController@destroyImage')
    ->name('documents.images.destroy');

Route::get('/documents/{documentTrashed}/restore', 'DocumentController@restore')
     ->name('documents.restore');

Route::put('/documents/{document}/support', 'DocumentController@updateSupport')
    ->name('documents.support');


// Sponsors
Route::resource('sponsors', 'SponsorController', [
    'except' => ['show']
]);

Route::put('/sponsors/{sponsor}/status', 'SponsorController@updateStatus')
    ->name('sponsors.status.update');


// Sponsor Members
Route::resource('sponsors.members', 'SponsorMemberController');

Route::put('/sponsors/{sponsor}/members/{member}/role', 'SponsorMemberController@updateRole')
    ->name('sponsors.members.role.update');


// Translations
Route::get('/translations', 'TranslationController@index');


// Users
Route::get('/users/{user}/settings', 'UserController@editSettings')
    ->name('users.settings.edit');

Route::get('/users/{user}/settings/account', 'UserController@editSettingsAccount')
    ->name('users.settings.account.edit');
Route::put('/users/{user}/settings/account', 'UserController@updateSettingsAccount')
    ->name('users.settings.account.update');

Route::get('/users/{user}/settings/password', 'UserController@editSettingsPassword')
    ->name('users.settings.password.edit');
Route::put('/users/{user}/settings/password', 'UserController@updateSettingsPassword')
    ->name('users.settings.password.update');

Route::get('/users/{user}/settings/notifications', 'UserController@editSettingsNotifications')
    ->name('users.settings.notifications.edit');
Route::put('/users/{user}/settings/notifications', 'UserController@updateSettingsNotifications')
    ->name('users.settings.notifications.update');


// Pages
Route::resource('pages', 'PageController', [
    'except' => ['index']
]);


// Settings
Route::get('/settings/site', 'SettingController@siteSettingsIndex')
    ->name('settings.site.index');

Route::get('/settings/pages', 'PageController@index')
    ->name('settings.pages.index');

Route::get('/settings/featured', 'SettingController@indexFeaturedDocuments')
    ->name('settings.featured-documents.index');

Route::put('/settings/featured/{document}', 'SettingController@updateFeaturedDocuments')
    ->name('settings.featured-documents.update');

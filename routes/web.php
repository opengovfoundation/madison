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

Route::bind('commentHidden', function ($value) {
    $comment = Annotation::withoutGlobalScope('visible')->find($value);
    if ($comment) {
        return $comment;
    }

    $comment = Annotation::withoutGlobalScope('visible')->where('str_id', $value)->first();
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
Route::post('documents/{document}/comments/{comment}/hide', 'CommentController@storeHidden')
    ->name('documents.comments.storeHidden');
Route::post('documents/{document}/comments/{commentHidden}/resolve', 'CommentController@storeResolve')
    ->name('documents.comments.storeResolve');


// Documents
Route::resource('documents', 'DocumentController', [
    'except' => ['create', 'edit']
]);

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

Route::get('/documents/{document}/manage/settings', 'DocumentController@manageSettings')
    ->name('documents.manage.settings');

Route::get('/documents/{document}/manage/comments', 'DocumentController@manageComments')
    ->name('documents.manage.comments');


// Sponsors
Route::get('/become-a-sponsor', 'SponsorController@info')
    ->name('sponsors.info');

Route::get('/sponsors/pending', 'SponsorController@awaitingApproval')
    ->name('sponsors.awaiting-approval');

Route::resource('sponsors', 'SponsorController', [
    'except' => ['index', 'destroy'],
]);

Route::get('/sponsors/{sponsor}/documents', 'SponsorController@documentsIndex')
    ->name('sponsors.documents.index');


// Sponsor Members
Route::resource('sponsors.members', 'SponsorMemberController');

Route::put('/sponsors/{sponsor}/members/{member}/role', 'SponsorMemberController@updateRole')
    ->name('sponsors.members.role.update');


// Translations
Route::get('/translations', 'TranslationController@index');


// Users
Route::get('/users/{user}/verify_email/{token}', 'UserController@verifyEmail')
    ->name('users.verify_email');

Route::post('/users/{user}/resend_email_verification', 'UserController@resendEmailVerification')
    ->name('users.resend_email_verification');

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

Route::get('/users/{user}/sponsors', 'UserController@sponsorsIndex')
    ->name('users.sponsors.index');


// Pages
Route::resource('pages', 'PageController', [
    'except' => ['index']
]);


// Settings
Route::get('/admin/site', 'AdminController@siteSettingsIndex')
    ->name('admin.site.index');

Route::put('/admin/site', 'AdminController@siteSettingsUpdate')
    ->name('admin.site.update');

Route::get('/admin/pages', 'PageController@index')
    ->name('admin.pages.index');

Route::get('/admin/featured', 'AdminController@indexFeaturedDocuments')
    ->name('admin.featured-documents.index');

Route::put('/admin/featured/{document}', 'AdminController@updateFeaturedDocuments')
    ->name('admin.featured-documents.update');

Route::post('/admin/featured/add', 'AdminController@addFeaturedDocument')
    ->name('admin.featured-documents.add');

Route::get('/admin/users', 'AdminController@usersIndex')
    ->name('admin.users.index');

Route::get('/admin/sponsors', 'AdminController@sponsorsIndex')
    ->name('admin.sponsors.index');

Route::put('/admin/sponsors/{sponsor}/status', 'AdminController@sponsorsPutStatus')
    ->name('admin.sponsors.status.update');

Route::post('/admin/users/{user}/admin', 'AdminController@usersPostAdmin')
    ->name('admin.users.postAdmin');

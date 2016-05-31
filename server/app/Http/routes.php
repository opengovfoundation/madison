<?php

use App\Models\Doc;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/**
 * Global Route Patterns
 */

Route::pattern('annotation', '[0-9a-zA-Z_-]+');
Route::pattern('comment', '[0-9a-zA-Z_-]+');
Route::pattern('doc', '[0-9a-zA-Z_-]+');
Route::pattern('docTrashed', '[0-9a-zA-Z_-]+');
Route::pattern('user', '[0-9]+');
Route::pattern('date', '[0-9]+');
Route::pattern('group', '[0-9]+');
Route::pattern('pagenum', '[0-9]+');
Route::pattern('image', '[a-zA-Z0-9-_]+\.[a-zA-Z0-9]{2,4}');
Route::pattern('state', Doc::validPublishStatesRoutePattern());

/**
 * Route - Model bindings
 */
Route::model('user', 'App\Models\User');
Route::model('user/edit', 'App\Models\User');
Route::model('page', 'App\Models\Page');
Route::bind('doc', function ($value) {
    $doc = Doc::find($value);
    if ($doc) {
        return $doc;
    }

    $doc = Doc::where('slug', $value)->first();
    if ($doc) {
        return $doc;
    }

    throw new NotFoundHttpException;
});
Route::bind('docTrashed', function ($value) {
    $doc = Doc::withTrashed()->find($value);
    if ($doc) {
        return $doc;
    }

    $doc = Doc::withTrashed()->where('slug', $value)->first();
    if ($doc) {
        return $doc;
    }

    throw new NotFoundHttpException;
});
Route::model('comment', 'App\Models\Annotation');

Route::group(['prefix' => 'api'], function() {
    // Vendor Settings
    Route::get('settings/vendors', function () {
        $uservoice = "";
        $ga = "";

        if (isset($_ENV['USERVOICE'])) {
            $uservoice = $_ENV['USERVOICE'];
        }

        if (isset($_ENV['GA'])) {
            $ga = $_ENV['GA'];
        }

        return ['uservoice' => $uservoice, 'ga' => $ga];
    });

    // Document Routes
    Route::get('docs/', 'DocumentController@getDocs');
    Route::post('docs/', 'DocumentController@postDocs');
    Route::get('docs/count', 'DocumentController@getDocCount');
    Route::get('docs/{state}', 'DocumentController@getDocs');
    Route::post('docs/featured', 'DocumentController@postFeatured');
    Route::put('docs/featured', 'DocumentController@putFeatured');
    Route::delete('docs/featured/{doc}', 'DocumentController@deleteFeatured');
    Route::get('docs/recent/{query?}', 'DocumentController@getRecent')->where('query', '[0-9]+');
    Route::get('docs/categories', 'DocumentController@getCategories');
    Route::get('docs/statuses', 'DocumentController@getAllStatuses');
    Route::get('docs/sponsors', 'DocumentController@getAllSponsors');
    Route::get('docs/featured', 'DocumentController@getFeatured');
    Route::get('docs/deleted', 'DocumentController@getDeletedDocs')->middleware(['auth']);
    Route::put('dates/{date}', 'DocumentController@putDate');

    // Single Doc Routes
    Route::post('docs/{doc}/support/', 'DocumentController@postSupport');
    Route::get('users/{user}/support/{doc}', 'UserController@getSupport');
    Route::get('docs/{doc}/categories', 'DocumentController@getCategories');
    Route::get('docs/{doc}/introtext', 'DocumentController@getIntroText');
    Route::get('docs/{doc}/content', 'DocumentController@getContent');
    Route::get('docs/{doc}/sponsor/{sponsor}', 'DocumentController@hasSponsor');
    Route::get('docs/{doc}/sponsor', 'DocumentController@getSponsor');
    Route::get('docs/{doc}/status', 'DocumentController@getStatus');
    Route::get('docs/{doc}/dates', 'DocumentController@getDates');
    Route::get('docs/{doc}/images/{image}','DocumentController@getImage');
    Route::get('docs/{doc}', 'DocumentController@getDoc');
    Route::put('docs/{doc}', 'DocumentController@update');
    Route::post('docs/{doc}/introtext', 'DocumentController@postIntroText');
    Route::post('docs/{doc}/title', 'DocumentController@postTitle');
    Route::post('docs/{doc}/sponsor', 'DocumentController@postSponsor');
    Route::post('docs/{doc}/publishstate', 'DocumentController@postPublishState');
    Route::post('docs/{doc}/slug', 'DocumentController@postSlug');
    Route::post('docs/{doc}/content', 'DocumentController@postContent');
    Route::put('docs/{doc}/content/{pagenum}', 'DocumentController@putContent');
    Route::delete('docs/{doc}/content/{pagenum}', 'DocumentController@deleteContent');
    Route::get('docs/embed/{doc}', 'DocumentController@getEmbedded');
    Route::get('docs/{docTrashed}/restore', 'DocumentController@getRestoreDoc');
    Route::delete('docs/{doc}', 'DocumentController@deleteDoc');
    Route::post('docs/{doc}/featured-image', 'DocumentController@uploadImage');
    Route::delete('docs/{doc}/featured-image', 'DocumentController@deleteImage');
    Route::post('docs/{doc}/categories', 'DocumentController@postCategories');
    Route::post('docs/{doc}/dates', 'DocumentController@postDate');
    Route::delete('docs/{doc}/dates/{dates}', 'DocumentController@deleteDate');
    Route::post('docs/{doc}/status', 'DocumentController@postStatus');
    Route::get('docs/{doc}/feed', 'DocumentController@getFeed');

    // Document Comment Routes
    Route::post('docs/{doc}/comments', 'CommentController@postIndex');
    Route::get('docs/{doc}/comments', 'CommentController@getIndex');;
    Route::get('docs/{doc}/comments/{comment}', 'CommentController@getComment');
    Route::post('docs/{doc}/comments/{comment}/likes', 'CommentController@postLikes');
    Route::post('docs/{doc}/comments/{comment}/flags', 'CommentController@postFlags');
    Route::post('docs/{doc}/comments/{comment}/comments', 'CommentController@postComments');
    Route::post('docs/{doc}/comments/{comment}/seen', 'CommentController@postSeen');

    // User Routes
    Route::get('user/{user}', 'UserController@getUser')->middleware(['auth']);
    Route::put('user/{user}', 'UserController@update')->middleware(['auth']);
    Route::get('user/verify/', 'UserController@getVerify')->middleware(['auth']);
    Route::post('user/verify/', 'UserController@postVerify')->middleware(['auth']);
    Route::get('user/admin/', 'UserController@getAdmins')->middleware(['auth']);
    Route::post('user/admin/', 'UserController@postAdmin')->middleware(['auth']);
    Route::get('user/current', 'UserController@getCurrent');
    Route::put('user/{user}/edit/email', 'UserController@editEmail');
    Route::get('user/{user}/docs', 'DocumentController@getUserDocuments');
    Route::get('user/{user}/notifications', 'UserController@getNotifications');
    Route::put('user/{user}/notifications', 'UserController@putNotifications');
    Route::get('user/{user}/groups', 'UserController@getGroups');
    Route::post('user/{user}/verify-email/resend', 'UserController@postResendVerifyEmail');
    Route::get('user/facebook-login', 'UserController@getFacebookLogin');
    Route::get('user/twitter-login', 'UserController@getTwitterLogin');
    Route::get('user/linkedin-login', 'UserController@getLinkedinLogin');
    Route::put('user/edit/{user}', 'UserController@putEdit');
    Route::post('user/verify-email', 'UserController@postVerifyEmail');
    Route::post('password/remind', 'RemindersController@postRemind');
    Route::post('password/reset',  'RemindersController@postReset');
    Route::get('user/sponsors/all', 'DocumentController@getAllSponsorsForUser');
    Route::get('sponsors/all', 'SponsorController@getAllSponsors');
    Route::post('verification/resend',  'RemindersController@postConfirmation');

    // Group Routes
    Route::post('groups', 'GroupController@store');
    Route::get('groups/{group?}', 'GroupController@getGroup');
    Route::put('groups/{group}', 'GroupController@update');
    Route::get('groups/verify/', 'GroupController@getVerify')->middleware(['auth']);
    Route::put('groups/verify/{groupId}', 'GroupController@putVerify')->middleware(['auth']);
    Route::post('groups/active/{groupId}', 'GroupController@setActiveGroup');
    Route::get('groups/roles', 'GroupController@getRoles');
    Route::delete('groups/{groupId}/members/{memberId}', 'GroupController@removeMember');
    Route::put('groups/{groupId}/invite', 'GroupController@processMemberInvite');
    Route::get('groups/{groupId}/members', 'GroupController@getMembers');
    Route::put('groups/{groupId}/members/{memberId}', 'GroupController@putMember');

    // Page Routes
    Route::get('pages/', 'PageController@index');
    Route::get('pages/{page}', 'PageController@show');
    Route::post('pages/', 'PageController@store');
    Route::put('pages/{page}', 'PageController@update');
    Route::delete('pages/{page}', 'PageController@destroy');
    Route::get('pages/{page}/content', 'PageController@getContent');
    Route::put('pages/{page}/content', 'PageController@updateContent');

    // User Login / Signup AJAX requests
    Route::post('user/login', 'UserController@postLogin');
    Route::post('user/signup', 'UserController@postSignup');

    // Auth Token Route
    //Route::get('/auth/token', 'AuthController@token');
    Route::get('/user/login', 'AuthController@login');
    Route::get('/user/logout', 'AuthController@logout');

    // Social Bot Routes
    // These deliver partial HTML pages to bots that are pre-rendering links in
    // apps. E.g., when the user pastes a link to Madison into a Facebook post.
    Route::get('social/docs/{doc}', 'DocumentController@getSocialDoc');
    Route::get('social/api/docs/{doc}/images/{image}','DocumentController@getImage');

    /**
     *   RSS Feed Route.
     */
    Route::get('docs/feed', function () {
        //Grab all documents
        $docs = Doc
            ::with('sponsors', 'content')
            ->latest('updated_at')
            ->take(20)
            ->get();

        $feed = App::make('feed');

        $feed->title = 'Madison Documents';
        $feed->description = 'Latest 20 documents in Madison';
        $feed->link = URL::to('rss');
        $feed->pubdate = $docs->first()->updated_at;
        $feed->lang = 'en';

        foreach ($docs as $doc) {
            $item = [];
            $item['title'] = $doc->title;
            $item['author'] = $doc->sponsors->first()->display_name;
            $item['link'] = $doc->url;
            $item['pubdate'] = $doc->updated_at;
            $item['description'] = $doc->title;
            $item['content'] = $doc->fullContentHtml();
            $item['enclosure'] = [];
            $item['category'] = '';

            $feed->addItem($item);
        }

        return $feed->render('atom');

    });
});

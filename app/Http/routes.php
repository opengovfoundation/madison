<?php

use App\Models\Doc;

/**
 * Partial Routing File.
 *
 * This file includes all page routes that have already been converted to the single-page-app-ness
 */

//Match any slug that does not begin with api/ and serve up index.html
  //Angular takes it from there and communicates via api
//Route::any('{slug}', function ($slug) {
//  if (!Config::get('app.debug')) {
//      return File::get(public_path().'/index.html');
//  } else {
//      return File::get(public_path().'/pre-build.html');
//  }
//})->where('slug', '^(?!(api|social)/)(.*)$');

/**
 * Sitemap Route
 * TODO: What are the performance implications of this?  Are the results cached?  I would assume so, but not sure.
 */
Route::get('sitemap', function () {

  $sitemap = App::make('sitemap');

  $pages = array('about', 'faq', 'user/login', 'user/signup');

  foreach ($pages as $page) {
      $sitemap->add($page);
  }

    $docs = Doc::all();

    foreach ($docs as $doc) {
        $sitemap->add('docs/'.$doc->slug);
    }

    $annotations = Annotation::all();

    foreach ($annotations as $annotation) {
        $sitemap->add('annotation/'.$annotation->id);
    }

    $users = User::all();

    foreach ($users as $user) {
        $sitemap->add('user/'.$user->id);
    }

    // show your sitemap (options: 'xml' (default), 'html', 'txt', 'ror-rss', 'ror-rdf')
    return $sitemap->render('xml');
});

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
Route::pattern('doc', '[0-9]+');
Route::pattern('user', '[0-9]+');
Route::pattern('date', '[0-9]+');
Route::pattern('group', '[0-9]+');
Route::pattern('image', '[a-zA-Z0-9-_]+\.[a-zA-Z0-9]{2,4}');
Route::pattern('state', Doc::validPublishStatesRoutePattern());

/**
 * Route - Model bindings
 */
Route::model('user', 'App\Models\User');
Route::model('user/edit', 'App\Models\User');

// Modal Routes
Route::get('modals/annotation_thanks', array(
    'uses' => 'ModalController@getAnnotationThanksModal',
    'before' => 'disable profiler',
));

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
Route::post('docs/{doc}/support/', 'DocumentController@postSupport')->middleware(['doc.access.read']);
Route::get('users/{user}/support/{doc}', 'UserController@getSupport')->middleware(['doc.access.read']);
Route::get('docs/{doc}/categories', 'DocumentController@getCategories')->middleware(['doc.access.read']);
Route::get('docs/{doc}/introtext', 'DocumentController@getIntroText')->middleware(['doc.access.read']);
Route::get('docs/{doc}/content', 'DocumentController@getContent')->middleware(['doc.access.read']);
Route::get('docs/{doc}/sponsor/{sponsor}', 'DocumentController@hasSponsor')->middleware(['doc.access.read']);
Route::get('docs/{doc}/sponsor', 'DocumentController@getSponsor')->middleware(['doc.access.read']);
Route::get('docs/{doc}/status', 'DocumentController@getStatus')->middleware(['doc.access.read']);
Route::get('docs/{doc}/dates', 'DocumentController@getDates')->middleware(['doc.access.read']);
Route::get('docs/{doc}/images/{image}','DocumentController@getImage')->middleware(['doc.access.read']);
Route::get('docs/{doc}', 'DocumentController@getDoc')->middleware(['doc.access.read']);
Route::put('docs/{doc}', 'DocumentController@update')->middleware(['doc.access.edit']);
Route::get('docs/slug/{slug}', 'DocumentController@getDocBySlug')->middleware(['doc.access.read']);
Route::post('docs/{doc}/introtext', 'DocumentController@postIntroText')->middleware(['doc.access.edit']);
Route::post('docs/{doc}/title', 'DocumentController@postTitle')->middleware(['doc.access.edit']);
Route::post('docs/{doc}/sponsor', 'DocumentController@postSponsor')->middleware(['doc.access.edit']);
Route::post('docs/{doc}/publishstate', 'DocumentController@postPublishState')->middleware(['doc.access.edit']);
Route::post('docs/{doc}/slug', 'DocumentController@postSlug')->middleware(['doc.access.edit']);
Route::post('docs/{doc}/content', 'DocumentController@postContent')->middleware(['doc.access.edit']);
Route::put('docs/{doc}/content/{page}', 'DocumentController@putContent')->middleware(['doc.access.edit']);
Route::delete('docs/{doc}/content/{page}', 'DocumentController@deleteContent')->middleware(['doc.access.edit']);
Route::get('docs/embed/{slug}', 'DocumentController@getEmbedded')->middleware(['doc.access.read']);
Route::get('docs/{doc}/activity', 'DocumentController@getActivity')->middleware(['doc.access.edit']);

// Annotation Action Routes
Route::post('docs/{doc}/annotations/{annotation}/likes', 'AnnotationController@postLikes');
Route::post('docs/{doc}/annotations/{annotation}/flags', 'AnnotationController@postFlags');
Route::post('docs/{doc}/annotations/{annotation}/seen', 'AnnotationController@postSeen');
Route::get('docs/{doc}/annotations/{annotation}/likes', 'AnnotationController@getLikes');
Route::get('docs/{doc}/annotations/{annotation}/flags', 'AnnotationController@getFlags');

// Annotation Comment Routes
Route::get('docs/{doc}/annotations/{annotation}/comments', 'AnnotationController@getComments');
Route::post('docs/{doc}/annotations/{annotation}/comments', 'AnnotationController@postComments');
Route::get('docs/{doc}/annotations/{annotation}/comments/{comment}', 'AnnotationController@getComments');

// Annotation Routes
Route::get('annotations/search', 'AnnotationController@getSearch');
Route::get('docs/{doc}/annotations/{annotation?}', 'AnnotationController@getIndex')->middleware(['doc.access.read']);
Route::post('docs/{doc}/annotations', 'AnnotationController@postIndex');
Route::put('docs/{doc}/annotations/{annotation}', 'AnnotationController@putIndex');
Route::delete('docs/{doc}/annotations/{annotation}', 'AnnotationController@deleteIndex');

// Document Comment Routes
Route::post('docs/{doc}/comments', 'CommentController@postIndex')->middleware(['doc.access.read']);
Route::get('docs/{doc}/comments', 'CommentController@getIndex')->middleware(['doc.access.read']);;
Route::get('docs/{doc}/comments/{comment?}', 'CommentController@getComment')->middleware(['doc.access.read']);
Route::post('docs/{doc}/comments/{comment}/likes', 'CommentController@postLikes')->middleware(['doc.access.read']);
Route::post('docs/{doc}/comments/{comment}/flags', 'CommentController@postFlags')->middleware(['doc.access.read']);
Route::post('docs/{doc}/comments/{comment}/comments', 'CommentController@postComments')->middleware(['doc.access.read']);
Route::post('docs/{doc}/comments/{comment}/seen', 'CommentController@postSeen')->middleware(['doc.access.read']);

// Document Activity Routes (combined Annotations / Comments)
Route::get('api/docs/{doc}/actions/', 'DocumentController@getActions')->middleware(['doc.access.read']);

// User Routes
Route::get('user/{user}', 'UserController@getUser')->middleware(['auth']);
Route::get('user/verify/', 'UserController@getVerify')->middleware(['auth']);
Route::post('user/verify/', 'UserController@postVerify')->middleware(['auth']);
Route::put('user/sponsor', 'SponsorController@putRequest');
Route::get('user/admin/', 'UserController@getAdmins')->middleware(['auth']);
Route::post('user/admin/', 'UserController@postAdmin')->middleware(['auth']);
Route::get('user/independent/verify/', 'UserController@getIndependentVerify')->middleware(['auth']);
Route::post('user/independent/verify/', 'UserController@postIndependentVerify')->middleware(['auth']);
Route::get('user/current', 'UserController@getCurrent');
Route::put('user/{user}/edit/email', 'UserController@editEmail');
Route::get('user/{user}/docs', 'DocumentController@getUserDocuments');
Route::get('user/{user}/notifications', 'UserController@getNotifications');
Route::put('user/{user}/notifications', 'UserController@putNotifications');
Route::get('user/{user}/groups', 'UserController@getGroups');
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
Route::get('groups/verify/', 'GroupController@getVerify')->middleware(['auth']);
Route::put('groups/verify/{groupId}', 'GroupController@putVerify')->middleware(['auth']);
Route::post('groups/active/{groupId}', 'GroupController@setActiveGroup');
Route::get('groups/roles', 'GroupController@getRoles');
Route::get('groups/{group?}', 'GroupController@getGroup');
Route::post('groups/{group?}', 'GroupController@postGroup');
Route::delete('groups/{groupId}/members/{memberId}', 'GroupController@removeMember');
Route::put('groups/{groupId}/invite', 'GroupController@processMemberInvite');
Route::get('groups/{groupId}/members', 'GroupController@getMembers');
Route::put('groups/{groupId}/members/{memberId}', 'GroupController@putMember');

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
Route::get('social/docs/{slug}', 'DocumentController@getSocialDoc')->middleware(['doc.access.read']);
Route::get('social/api/docs/{doc}/images/{image}','DocumentController@getImage')->middleware(['doc.access.read']);

/**
 *   RSS Feed Route.
 */
Route::get('docs/feed', function () {
    //Grab all documents
    $docs = Doc::with('sponsor', 'content')->orderBy('updated_at', 'DESC')->take(20)->get();

    $feed = Feed::make();

    $feed->title = 'Madison Documents';
    $feed->description = 'Latest 20 documents in Madison';
    $feed->link = URL::to('rss');
    $feed->pubdate = $docs->first()->updated_at;
    $feed->lang = 'en';

    foreach ($docs as $doc) {
        $sponsor = $doc->sponsor->first();
        if ($sponsor instanceof User) {
            $display_name = $sponsor->fname.' '.$sponsor->lname;
        } elseif ($sponsor instanceof Group) {
            $display_name = $sponsor->display_name;
        } else {
            $display_name = '';
        }

        $item = array();
        $item['title'] = $doc->title;
        $item['author'] = $display_name;
        $item['link'] = URL::to('docs/'.$doc->slug);
        $item['pubdate'] = $doc->updated_at;
        $item['description'] = $doc->title;
        $item['content'] = $doc->content->html();

        array_push($feed->items, $item);
    }

    return $feed->render('atom');

});

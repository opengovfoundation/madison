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

//})->where('slug', '^(?!api/)(.*)$');

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

/*
*   Global Route Patterns
*/

Route::pattern('annotation', '[0-9a-zA-Z_-]+');
Route::pattern('comment', '[0-9a-zA-Z_-]+');
Route::pattern('doc', '[0-9]+');
Route::pattern('user', '[0-9]+');
Route::pattern('date', '[0-9]+');
Route::pattern('group', '[0-9]+');
Route::pattern('image', '[a-zA-Z0-9-_]+\.[a-zA-Z0-9]{2,4}');
Route::pattern('state', Doc::validStatesPattern());

/*
*   Route - Model bindings
*/
Route::model('user', 'App\Models\User');
Route::model('user/edit', 'App\Models\User');

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
*/

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

Route::post('groups/member/{memberId}/role', 'GroupsController@changeMemberRole');
Route::post('groups/active/{groupId}', 'GroupsController@setActiveGroup');

Route::get('groups/roles', 'GroupsController@getRoles');
Route::get('groups/{group?}', 'GroupsController@getGroup');
Route::post('groups/{group?}', 'GroupsController@postGroup');
Route::delete('groups/{groupId}/members/{memberId}', 'GroupsController@removeMember');
Route::put('groups/{groupId}/invite', 'GroupsController@processMemberInvite');
Route::get('groups/{groupId}/members', 'GroupsController@getMembers');
Route::put('groups/{groupId}/members/{memberId}', 'GroupsController@putMember');

//Document Routes
//Route::get('docs', 'DocController@index');
//Route::get('docs/{slug}', 'DocController@index')->middleware(['doc.access.read']);
//Route::get('docs/embed/{slug}', 'DocController@getEmbedded')->middleware(['doc.access.read']);
Route::get('docs/{slug}/feed', 'DocController@getFeed')->middleware(['doc.access.read']);
//Route::get('documents/search', 'DocumentsController@getSearch');
//Route::get('documents', 'DocumentsController@listDocuments');
//Route::get('documents/view/{documentId}', 'DocumentsController@viewDocument');
//Route::get('documents/edit/{documentId}', 'DocumentsController@editDocument');
//Route::put('documents/edit/{documentId}', 'DocumentsController@saveDocumentEdits');
//Route::post('documents/create', 'DocumentsController@createDocument');
//Route::post('documents/save', 'DocumentsController@saveDocument');
//Route::delete('/documents/delete/{slug}', 'DocumentsController@deleteDocument')->middleware(['doc.access.edit']);

//User Routes
//Route::get('user/{user}', 'UserController@getIndex');
//Route::get('user/edit/{user}', 'UserController@getEdit');
Route::put('user/edit/{user}', 'UserController@putEdit');
Route::post('user/verify-email', 'UserController@postVerify');

Route::post('password/remind', 'RemindersController@postRemind');
Route::post('password/reset',  'RemindersController@postReset');

// Confirmation email resend
Route::post('verification/resend',  'RemindersController@postConfirmation');

//Annotation Routes
//Route::get('annotation/{annotation}', 'AnnotationController@getIndex');

//Dashboard Routes
//Route::controller('dashboard', 'DashboardController');

//Api Routes
// Document API Routes
Route::get('user/sponsors/all', 'DocumentApiController@getAllSponsorsForUser');
Route::get('sponsors/all', 'SponsorApiController@getAllSponsors');

//Annotation Action Routes
Route::post('docs/{doc}/annotations/{annotation}/likes', 'AnnotationApiController@postLikes');
Route::post('docs/{doc}/annotations/{annotation}/flags', 'AnnotationApiController@postFlags');
Route::post('docs/{doc}/annotations/{annotation}/seen', 'AnnotationApiController@postSeen');
Route::get('docs/{doc}/annotations/{annotation}/likes', 'AnnotationApiController@getLikes');
Route::get('docs/{doc}/annotations/{annotation}/flags', 'AnnotationApiController@getFlags');

//Annotation Comment Routes
Route::get('docs/{doc}/annotations/{annotation}/comments', 'AnnotationApiController@getComments');
Route::post('docs/{doc}/annotations/{annotation}/comments', 'AnnotationApiController@postComments');
Route::get('docs/{doc}/annotations/{annotation}/comments/{comment}', 'AnnotationApiController@getComments');

//Annotation Routes
Route::get('annotations/search', 'AnnotationApiController@getSearch');
Route::get('docs/{doc}/annotations/{annotation?}', 'AnnotationApiController@getIndex')->middleware(['doc.access.read']);
Route::post('docs/{doc}/annotations', 'AnnotationApiController@postIndex');
Route::put('docs/{doc}/annotations/{annotation}', 'AnnotationApiController@putIndex');
Route::delete('docs/{doc}/annotations/{annotation}', 'AnnotationApiController@deleteIndex');

//Document Routes
Route::get('docs/slug/{slug}', 'DocumentsController@getDocument')->middleware(['doc.access.read']);
Route::get('docs/{doc}/content', 'DocumentsController@getDocumentContent')->middleware(['doc.access.read']);

//Document Comment Routes
Route::post('docs/{doc}/comments', 'CommentApiController@postIndex')->middleware(['doc.access.read']);
Route::get('docs/{doc}/comments', 'CommentApiController@getIndex')->middleware(['doc.access.read']);;
Route::get('docs/{doc}/comments/{comment?}', 'CommentApiController@getComment')->middleware(['doc.access.read']);
Route::post('docs/{doc}/comments/{comment}/likes', 'CommentApiController@postLikes')->middleware(['doc.access.read']);
Route::post('docs/{doc}/comments/{comment}/flags', 'CommentApiController@postFlags')->middleware(['doc.access.read']);
Route::post('docs/{doc}/comments/{comment}/comments', 'CommentApiController@postComments')->middleware(['doc.access.read']);
Route::post('docs/{doc}/comments/{comment}/seen', 'CommentApiController@postSeen')->middleware(['doc.access.read']);

//Document Support / Oppose routes
Route::post('docs/{doc}/support/', 'DocController@postSupport')->middleware(['doc.access.read']);
Route::get('users/{user}/support/{doc}', 'UserApiController@getSupport')->middleware(['doc.access.read']);

//Document Api Routes
Route::get('docs/recent/{query?}', 'DocumentApiController@getRecent')->where('query', '[0-9]+');
Route::get('docs/active/{query?}', 'DocumentsController@getActive')->where('query', '[0-9]+');
Route::get('docs/categories', 'DocumentApiController@getCategories');
Route::get('docs/statuses', 'DocumentApiController@getAllStatuses');
Route::get('docs/sponsors', 'DocumentApiController@getAllSponsors');
Route::get('docs/featured', 'DocumentsController@getFeatured');
Route::get('docs/deleted', 'DocumentApiController@getDeletedDocs')->middleware(['auth']);

Route::get('docs/count', 'DocumentApiController@getDocCount');
Route::get('docs/', 'DocumentApiController@getDocs');
Route::get('docs/{state}', 'DocumentApiController@getDocs');
Route::put('dates/{date}', 'DocumentApiController@putDate');

Route::post('docs/featured', 'DocumentsController@postFeatured');
Route::put('docs/featured', 'DocumentsController@putFeatured');
Route::delete('docs/featured/{doc}', 'DocumentsController@deleteFeatured');

Route::get('docs/{doc}/categories', 'DocumentApiController@getCategories')->middleware(['doc.access.read']);
Route::get('docs/{doc}/introtext', 'DocumentApiController@getIntroText')->middleware(['doc.access.read']);
Route::get('docs/{doc}/sponsor/{sponsor}', 'DocumentApiController@hasSponsor')->middleware(['doc.access.read']);
Route::get('docs/{doc}/sponsor', 'DocumentApiController@getSponsor')->middleware(['doc.access.read']);
Route::get('docs/{doc}/status', 'DocumentApiController@getStatus')->middleware(['doc.access.read']);
Route::get('docs/{doc}/dates', 'DocumentApiController@getDates')->middleware(['doc.access.read']);
Route::get('docs/{doc}/images/{image}','DocumentsController@getImage')->middleware(['doc.access.read']);
Route::get('docs/{doc}', 'DocumentApiController@getDoc')->middleware(['doc.access.read']);

Route::post('docs/', 'DocumentApiController@postDocs');
Route::post('docs/{doc}/introtext', 'DocumentApiController@postIntroText')->middleware(['doc.access.edit']);
Route::post('docs/{doc}/title', 'DocumentApiController@postTitle')->middleware(['doc.access.edit']);
Route::post('docs/{doc}/sponsor', 'DocumentApiController@postSponsor')->middleware(['doc.access.edit']);
Route::post('docs/{doc}/publishstate', 'DocumentApiController@postPublishState')->middleware(['doc.access.edit']);
Route::post('docs/{doc}/slug', 'DocumentApiController@postSlug')->middleware(['doc.access.edit']);
Route::post('docs/{doc}/content', 'DocumentApiController@postContent')->middleware(['doc.access.edit']);
Route::put('docs/{doc}/content/{page}', 'DocumentApiController@putContent')->middleware(['doc.access.edit']);
Route::delete('docs/{doc}/content/{page}', 'DocumentApiController@deleteContent')->middleware(['doc.access.edit']);

Route::post('docs/{doc}/featured-image', 'DocumentsController@uploadImage')->middleware(['doc.access.edit']);
Route::post('docs/{doc}/status', 'DocumentApiController@postStatus')->middleware(['doc.access.edit']);
Route::delete('docs/{doc}', 'DocumentApiController@deleteDoc')->middleware(['doc.access.edit']);
Route::put('docs/{doc}/restore', 'DocumentApiController@getRestoreDoc')->middleware(['doc.access.edit']);
Route::delete('docs/{doc}/featured-image', 'DocumentsController@deleteImage')->middleware(['doc.access.edit']);
Route::delete('docs/{doc}/dates/{date}', 'DocumentApiController@deleteDate')->middleware(['doc.access.edit']);
Route::post('docs/{doc}/dates', 'DocumentApiController@postDate')->middleware(['doc.access.edit']);
Route::post('docs/{doc}/categories', 'DocumentApiController@postCategories')->middleware(['doc.access.edit']);


//User Routes
Route::get('user/{user}', 'UserApiController@getUser');
Route::get('user/verify/', 'UserApiController@getVerify');
Route::post('user/verify/', 'UserApiController@postVerify');
Route::put('user/sponsor', 'SponsorApiController@putRequest');
Route::get('user/admin/', 'UserApiController@getAdmins');
Route::post('user/admin/', 'UserApiController@postAdmin');
Route::get('user/independent/verify/', 'UserApiController@getIndependentVerify');
Route::post('user/independent/verify/', 'UserApiController@postIndependentVerify');
Route::get('user/current', 'UserController@getCurrent');
Route::put('user/{user}/edit/email', 'UserController@editEmail');
Route::get('user/{user}/docs', 'DocumentsController@listDocuments');
Route::get('user/{user}/notifications', 'UserController@getNotifications');
Route::put('user/{user}/notifications', 'UserController@putNotifications');
Route::get('user/{user}/groups', 'UserController@getGroups');
Route::get('user/facebook-login', 'UserController@getFacebookLogin');
Route::get('user/twitter-login', 'UserController@getTwitterLogin');
Route::get('user/linkedin-login', 'UserController@getLinkedinLogin');

// Group Routes
Route::get('groups/verify/', 'GroupsApiController@getVerify');
Route::put('groups/verify/{groupId}', 'GroupsApiController@putVerify');

// User Login / Signup AJAX requests
Route::get('user/login', 'UserManageApiController@getLogin');
Route::post('user/login', 'UserManageApiController@postLogin');
Route::get('user/signup', 'UserManageApiController@getSignup');
Route::post('user/signup', 'UserManageApiController@postSignup');

//Auth Token Route
//Route::get('/auth/token', 'AuthController@token');
Route::get('/user/login', 'AuthController@login');
Route::get('/user/logout', 'AuthController@logout');

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



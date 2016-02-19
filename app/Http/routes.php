<?php

use App\Models\Doc;

/**
 * Partial Routing File.
 *
 * This file includes all page routes that have already been converted to the single-page-app-ness
 */

//Match any slug that does not begin with api/ and serve up index.html
  //Angular takes it from there and communicates via api
Route::any('{slug}', function ($slug) {
  if (!Config::get('app.debug')) {
      return File::get(public_path().'/index.html');
  } else {
      return File::get(public_path().'/pre-build.html');
  }

})->where('slug', '^(?!api/)(.*)$');

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
Route::get('api/settings/vendors', function () {
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
Route::post('api/groups/active/{groupId}', 'GroupsController@setActiveGroup');

Route::get('api/groups/roles', 'GroupsController@getRoles');
Route::get('api/groups/{group?}', 'GroupsController@getGroup');
Route::post('api/groups/{group?}', 'GroupsController@postGroup');
Route::delete('api/groups/{groupId}/members/{memberId}', 'GroupsController@removeMember');
Route::put('api/groups/{groupId}/invite', 'GroupsController@processMemberInvite');
Route::get('api/groups/{groupId}/members', 'GroupsController@getMembers');
Route::put('api/groups/{groupId}/members/{memberId}', 'GroupsController@putMember');

//Document Routes
Route::get('docs', 'DocController@index');
Route::get('docs/{slug}', 'DocController@index')->middleware(['doc.access.read']);
Route::get('docs/embed/{slug}', 'DocController@getEmbedded')->middleware(['doc.access.read']);
Route::get('api/docs/{slug}/feed', 'DocController@getFeed')->middleware(['doc.access.read']);
Route::get('documents/search', 'DocumentsController@getSearch');
Route::get('documents', 'DocumentsController@listDocuments');
Route::get('documents/view/{documentId}', 'DocumentsController@viewDocument');
Route::get('documents/edit/{documentId}', 'DocumentsController@editDocument');
Route::put('documents/edit/{documentId}', 'DocumentsController@saveDocumentEdits');
Route::post('documents/create', 'DocumentsController@createDocument');
Route::post('documents/save', 'DocumentsController@saveDocument');
Route::delete('/documents/delete/{slug}', 'DocumentsController@deleteDocument')->middleware(['doc.access.edit']);

//User Routes
Route::get('user/{user}', 'UserController@getIndex');
Route::get('user/edit/{user}', 'UserController@getEdit');
Route::put('api/user/edit/{user}', 'UserController@putEdit');
Route::post('api/user/verify-email', 'UserController@postVerify');

Route::post('api/password/remind', 'RemindersController@postRemind');
Route::post('api/password/reset',  'RemindersController@postReset');

// Confirmation email resend
Route::post('api/verification/resend',  'RemindersController@postConfirmation');

//Annotation Routes
Route::get('annotation/{annotation}', 'AnnotationController@getIndex');

//Dashboard Routes
Route::controller('dashboard', 'DashboardController');

//Api Routes
// Document API Routes
Route::get('api/user/sponsors/all', 'DocumentApiController@getAllSponsorsForUser');
Route::get('api/sponsors/all', 'SponsorApiController@getAllSponsors');

//Annotation Action Routes
Route::post('api/docs/{doc}/annotations/{annotation}/likes', 'AnnotationApiController@postLikes');
Route::post('api/docs/{doc}/annotations/{annotation}/flags', 'AnnotationApiController@postFlags');
Route::post('api/docs/{doc}/annotations/{annotation}/seen', 'AnnotationApiController@postSeen');
Route::get('api/docs/{doc}/annotations/{annotation}/likes', 'AnnotationApiController@getLikes');
Route::get('api/docs/{doc}/annotations/{annotation}/flags', 'AnnotationApiController@getFlags');

//Annotation Comment Routes
Route::get('api/docs/{doc}/annotations/{annotation}/comments', 'AnnotationApiController@getComments');
Route::post('api/docs/{doc}/annotations/{annotation}/comments', 'AnnotationApiController@postComments');
Route::get('api/docs/{doc}/annotations/{annotation}/comments/{comment}', 'AnnotationApiController@getComments');

//Annotation Routes
Route::get('api/annotations/search', 'AnnotationApiController@getSearch');
Route::get('api/docs/{doc}/annotations/{annotation?}', 'AnnotationApiController@getIndex')->middleware(['doc.access.read']);
Route::post('api/docs/{doc}/annotations', 'AnnotationApiController@postIndex');
Route::put('api/docs/{doc}/annotations/{annotation}', 'AnnotationApiController@putIndex');
Route::delete('api/docs/{doc}/annotations/{annotation}', 'AnnotationApiController@deleteIndex');

//Document Routes
Route::get('api/docs/slug/{slug}', 'DocumentsController@getDocument')->middleware(['doc.access.read']);
Route::get('api/docs/{doc}/content', 'DocumentsController@getDocumentContent')->middleware(['doc.access.read']);

//Document Comment Routes
Route::post('api/docs/{doc}/comments', 'CommentApiController@postIndex')->middleware(['doc.access.read']);
Route::get('api/docs/{doc}/comments', 'CommentApiController@getIndex')->middleware(['doc.access.read']);;
Route::get('api/docs/{doc}/comments/{comment?}', 'CommentApiController@getComment')->middleware(['doc.access.read']);
Route::post('api/docs/{doc}/comments/{comment}/likes', 'CommentApiController@postLikes')->middleware(['doc.access.read']);
Route::post('api/docs/{doc}/comments/{comment}/flags', 'CommentApiController@postFlags')->middleware(['doc.access.read']);
Route::post('api/docs/{doc}/comments/{comment}/comments', 'CommentApiController@postComments')->middleware(['doc.access.read']);
Route::post('api/docs/{doc}/comments/{comment}/seen', 'CommentApiController@postSeen')->middleware(['doc.access.read']);

//Document Support / Oppose routes
Route::post('api/docs/{doc}/support/', 'DocController@postSupport')->middleware(['doc.access.read']);
Route::get('api/users/{user}/support/{doc}', 'UserApiController@getSupport')->middleware(['doc.access.read']);

//Document Api Routes
Route::get('api/docs/recent/{query?}', 'DocumentApiController@getRecent')->where('query', '[0-9]+');
Route::get('api/docs/active/{query?}', 'DocumentsController@getActive')->where('query', '[0-9]+');
Route::get('api/docs/categories', 'DocumentApiController@getCategories');
Route::get('api/docs/statuses', 'DocumentApiController@getAllStatuses');
Route::get('api/docs/sponsors', 'DocumentApiController@getAllSponsors');
Route::get('api/docs/featured', 'DocumentsController@getFeatured');
Route::get('api/docs/deleted', 'DocumentApiController@getDeletedDocs')->middleware(['auth']);

Route::get('api/docs/count', 'DocumentApiController@getDocCount');
Route::get('api/docs/', 'DocumentApiController@getDocs');
Route::get('api/docs/{state}', 'DocumentApiController@getDocs');
Route::put('api/dates/{date}', 'DocumentApiController@putDate');

Route::post('api/docs/featured', 'DocumentsController@postFeatured');
Route::put('api/docs/featured', 'DocumentsController@putFeatured');
Route::delete('api/docs/featured/{doc}', 'DocumentsController@deleteFeatured');

Route::get('api/docs/{doc}/categories', 'DocumentApiController@getCategories')->middleware(['doc.access.read']);
Route::get('api/docs/{doc}/introtext', 'DocumentApiController@getIntroText')->middleware(['doc.access.read']);
Route::get('api/docs/{doc}/sponsor/{sponsor}', 'DocumentApiController@hasSponsor')->middleware(['doc.access.read']);
Route::get('api/docs/{doc}/sponsor', 'DocumentApiController@getSponsor')->middleware(['doc.access.read']);
Route::get('api/docs/{doc}/status', 'DocumentApiController@getStatus')->middleware(['doc.access.read']);
Route::get('api/docs/{doc}/dates', 'DocumentApiController@getDates')->middleware(['doc.access.read']);
Route::get('api/docs/{doc}/images/{image}','DocumentsController@getImage')->middleware(['doc.access.read']);
Route::get('api/docs/{doc}', 'DocumentApiController@getDoc')->middleware(['doc.access.read']);

Route::post('api/docs/', 'DocumentApiController@postDocs');
Route::post('api/docs/{doc}/introtext', 'DocumentApiController@postIntroText')->middleware(['doc.access.edit']);
Route::post('api/docs/{doc}/title', 'DocumentApiController@postTitle')->middleware(['doc.access.edit']);
Route::post('api/docs/{doc}/sponsor', 'DocumentApiController@postSponsor')->middleware(['doc.access.edit']);
Route::post('api/docs/{doc}/publishstate', 'DocumentApiController@postPublishState')->middleware(['doc.access.edit']);
Route::post('api/docs/{doc}/slug', 'DocumentApiController@postSlug')->middleware(['doc.access.edit']);
Route::post('api/docs/{doc}/content', 'DocumentApiController@postContent')->middleware(['doc.access.edit']);
Route::put('api/docs/{doc}/content/{page}', 'DocumentApiController@putContent')->middleware(['doc.access.edit']);
Route::delete('api/docs/{doc}/content/{page}', 'DocumentApiController@deleteContent')->middleware(['doc.access.edit']);

Route::post('api/docs/{doc}/featured-image', 'DocumentsController@uploadImage')->middleware(['doc.access.edit']);
Route::post('api/docs/{doc}/status', 'DocumentApiController@postStatus')->middleware(['doc.access.edit']);
Route::delete('api/docs/{doc}', 'DocumentApiController@deleteDoc')->middleware(['doc.access.edit']);
Route::put('api/docs/{doc}/restore', 'DocumentApiController@getRestoreDoc')->middleware(['doc.access.edit']);
Route::delete('api/docs/{doc}/featured-image', 'DocumentsController@deleteImage')->middleware(['doc.access.edit']);
Route::delete('api/docs/{doc}/dates/{date}', 'DocumentApiController@deleteDate')->middleware(['doc.access.edit']);
Route::post('api/docs/{doc}/dates', 'DocumentApiController@postDate')->middleware(['doc.access.edit']);
Route::post('api/docs/{doc}/categories', 'DocumentApiController@postCategories')->middleware(['doc.access.edit']);


//User Routes
Route::get('api/user/{user}', 'UserApiController@getUser');
Route::get('api/user/verify/', 'UserApiController@getVerify');
Route::post('api/user/verify/', 'UserApiController@postVerify');
Route::put('api/user/sponsor', 'SponsorApiController@putRequest');
Route::get('api/user/admin/', 'UserApiController@getAdmins');
Route::post('api/user/admin/', 'UserApiController@postAdmin');
Route::get('api/user/independent/verify/', 'UserApiController@getIndependentVerify');
Route::post('api/user/independent/verify/', 'UserApiController@postIndependentVerify');
Route::get('api/user/current', 'UserController@getCurrent');
Route::put('api/user/{user}/edit/email', 'UserController@editEmail');
Route::get('api/user/{user}/docs', 'DocumentsController@listDocuments');
Route::get('api/user/{user}/notifications', 'UserController@getNotifications');
Route::put('api/user/{user}/notifications', 'UserController@putNotifications');
Route::get('api/user/{user}/groups', 'UserController@getGroups');
Route::get('api/user/facebook-login', 'UserController@getFacebookLogin');
Route::get('api/user/twitter-login', 'UserController@getTwitterLogin');
Route::get('api/user/linkedin-login', 'UserController@getLinkedinLogin');

// Group Routes
Route::get('api/groups/verify/', 'GroupsApiController@getVerify');
Route::put('api/groups/verify/{groupId}', 'GroupsApiController@putVerify');

// User Login / Signup AJAX requests
Route::get('api/user/login', 'UserManageApiController@getLogin');
Route::post('api/user/login', 'UserManageApiController@postLogin');
Route::get('api/user/signup', 'UserManageApiController@getSignup');
Route::post('api/user/signup', 'UserManageApiController@postSignup');

//Auth Token Route
Route::get('/auth/token', 'AuthController@token');
Route::get('/api/user/login', 'AuthController@login');
Route::get('/api/user/logout', 'AuthController@logout');

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

<?php

/**
 *   Include all partials in app/routes/.
 */
foreach (File::allFiles(__DIR__.'/routes') as $partial) {
    require_once $partial->getPathname();
}

/*
*   Global Route Patterns
*/

Route::pattern('annotation', '[0-9a-zA-Z_-]+');
Route::pattern('comment', '[0-9a-zA-Z_-]+');
Route::pattern('doc', '[0-9]+');
Route::pattern('user', '[0-9]+');
Route::pattern('date', '[0-9]+');
Route::pattern('group', '[0-9]+');

/*
*   Route - Model bindings
*/
Route::model('user', 'User');
Route::model('user/edit', 'User');

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
Route::get('docs/{slug}', 'DocController@index');
Route::get('docs/embed/{slug}', 'DocController@getEmbedded');
Route::get('api/docs/{slug}/feed', 'DocController@getFeed');
Route::get('documents/search', 'DocumentsController@getSearch');
Route::get('documents', 'DocumentsController@listDocuments');
Route::get('documents/view/{documentId}', 'DocumentsController@viewDocument');
Route::get('documents/edit/{documentId}', 'DocumentsController@editDocument');
Route::put('documents/edit/{documentId}', 'DocumentsController@saveDocumentEdits');
Route::post('documents/create', 'DocumentsController@createDocument');
Route::post('documents/save', 'DocumentsController@saveDocument');
Route::delete('/documents/delete/{slug}', 'DocumentsController@deleteDocument');
Route::get('/documents/sponsor/request', 'SponsorController@getRequest');
Route::post('/documents/sponsor/request', 'SponsorController@postRequest');

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
    Route::post('api/docs/{doc}/annotations/{annotation}/dislikes', 'AnnotationApiController@postDislikes');
    Route::post('api/docs/{doc}/annotations/{annotation}/flags', 'AnnotationApiController@postFlags');
    Route::post('api/docs/{doc}/annotations/{annotation}/seen', 'AnnotationApiController@postSeen');
    Route::get('api/docs/{doc}/annotations/{annotation}/likes', 'AnnotationApiController@getLikes');
    Route::get('api/docs/{doc}/annotations/{annotation}/dislikes', 'AnnotationApiController@getDislikes');
    Route::get('api/docs/{doc}/annotations/{annotation}/flags', 'AnnotationApiController@getFlags');

    //Annotation Comment Routes
    Route::get('api/docs/{doc}/annotations/{annotation}/comments', 'AnnotationApiController@getComments');
    Route::post('api/docs/{doc}/annotations/{annotation}/comments', 'AnnotationApiController@postComments');
    Route::get('api/docs/{doc}/annotations/{annotation}/comments/{comment}', 'AnnotationApiController@getComments');

    //Annotation Routes
    Route::get('api/annotations/search', 'AnnotationApiController@getSearch');
    Route::get('api/docs/{doc}/annotations/{annotation?}', 'AnnotationApiController@getIndex');
    Route::post('api/docs/{doc}/annotations', 'AnnotationApiController@postIndex');
    Route::put('api/docs/{doc}/annotations/{annotation}', 'AnnotationApiController@putIndex');
    Route::delete('api/docs/{doc}/annotations/{annotation}', 'AnnotationApiController@deleteIndex');

    //Document Routes
    Route::get('api/docs/slug/{slug}', 'DocumentsController@getDocument');
    Route::get('api/docs/{doc}/content', 'DocumentsController@getDocumentContent');

    //Document Comment Routes
    Route::post('api/docs/{doc}/comments', 'CommentApiController@postIndex');
    Route::get('api/docs/{doc}/comments', 'CommentApiController@getIndex');
    Route::get('api/docs/{doc}/comments/{comment?}', 'CommentApiController@getComment');
    Route::post('api/docs/{doc}/comments/{comment}/likes', 'CommentApiController@postLikes');
    Route::post('api/docs/{doc}/comments/{comment}/dislikes', 'CommentApiController@postDislikes');
    Route::post('api/docs/{doc}/comments/{comment}/flags', 'CommentApiController@postFlags');
    Route::post('api/docs/{doc}/comments/{comment}/comments', 'CommentApiController@postComments');
    Route::post('api/docs/{doc}/comments/{comment}/seen', 'CommentApiController@postSeen');

    //Document Support / Oppose routes
    Route::post('api/docs/{doc}/support/', 'DocController@postSupport');
    Route::get('api/users/{user}/support/{doc}', 'UserApiController@getSupport');

    //Document Api Routes
    Route::get('api/docs/recent/{query?}', 'DocumentApiController@getRecent')->where('query', '[0-9]+');
    Route::get('api/docs/active/{query?}', 'DocumentsController@getActive')->where('query', '[0-9]+');
    Route::get('api/docs/categories', 'DocumentApiController@getCategories');
    Route::get('api/docs/statuses', 'DocumentApiController@getAllStatuses');
    Route::get('api/docs/sponsors', 'DocumentApiController@getAllSponsors');
    Route::get('api/docs/{doc}/categories', 'DocumentApiController@getCategories');
    Route::post('api/docs/{doc}/categories', 'DocumentApiController@postCategories');
    Route::get('api/docs/{doc}/introtext', 'DocumentApiController@getIntroText');
    Route::post('api/docs/{doc}/introtext', 'DocumentApiController@postIntroText');
    Route::get('api/docs/{doc}/sponsor/{sponsor}', 'DocumentApiController@hasSponsor');
    Route::get('api/docs/{doc}/sponsor', 'DocumentApiController@getSponsor');
    Route::post('api/docs/{doc}/sponsor', 'DocumentApiController@postSponsor');
    Route::get('api/docs/{doc}/status', 'DocumentApiController@getStatus');
    Route::post('api/docs/{doc}/status', 'DocumentApiController@postStatus');
    Route::get('api/docs/{doc}/dates', 'DocumentApiController@getDates');
    Route::post('api/docs/{doc}/dates', 'DocumentApiController@postDate');
    Route::put('api/dates/{date}', 'DocumentApiController@putDate');
    Route::delete('api/docs/{doc}/dates/{date}', 'DocumentApiController@deleteDate');
    Route::get('api/docs/{doc}', 'DocumentApiController@getDoc');
    Route::post('api/docs/{doc}/title', 'DocumentApiController@postTitle');
    Route::post('api/docs/{doc}/slug', 'DocumentApiController@postSlug');
    Route::post('api/docs/{doc}/content', 'DocumentApiController@postContent');
    Route::post('api/docs/{doc}/featured-image', 'DocumentsController@uploadImage');
    Route::delete('api/docs/{doc}/featured-image', 'DocumentsController@deleteImage');
    Route::get('api/docs/featured', 'DocumentsController@getFeatured');
    Route::post('api/docs/featured', 'DocumentsController@postFeatured');
    Route::get('api/docs/', 'DocumentApiController@getDocs');
    Route::post('api/docs/', 'DocumentApiController@postDocs');

    //User Routes
    Route::get('api/user/{user}', 'UserApiController@getUser');
    Route::get('api/user/verify/', 'UserApiController@getVerify');
    Route::post('api/user/verify/', 'UserApiController@postVerify');
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
    Route::post('api/groups/verify/', 'GroupsApiController@postVerify');

    // User Login / Signup AJAX requests
    Route::get('api/user/login', 'UserManageApiController@getLogin');
    Route::post('api/user/login', 'UserManageApiController@postLogin');
    Route::get('api/user/signup', 'UserManageApiController@getSignup');
    Route::post('api/user/signup', 'UserManageApiController@postSignup');

//Auth Token Route
Route::get('/auth/token', 'AuthController@token');
Route::get('/api/user/login', 'AuthController@login');
Route::get('/api/user/logout', 'AuthController@logout');

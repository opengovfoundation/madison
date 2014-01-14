<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
*/

//Static Pages
Route::get('about', 'PageController@getAbout');
Route::get('faq', 'PageController@faq');
Route::get('/', 'PageController@home');

//Document Routes
Route::get('docs', 'DocController@index');
Route::get('docs/{slug}', 'DocController@index');

//User Routes
Route::get('user/{id}', 'UserController@getIndex')->where(array('id' => '[0-9]+'));
Route::controller('user', 'UserController');

//Note Routes
Route::get('note/{id}', 'NoteController@getIndex')->where(array('id' => '[0-9a-zA-Z_-]+'));
Route::post('note/{id}', 'NoteController@postIndex')->where(array('id' => '[0-9a-zA-Z_-]+'));
Route::put('note/{id}', 'NoteController@putIndex')->where(array('id' => '[0-9a-zA-Z_-]+'));
Route::controller('note', 'NoteController');

//Dashboard Routes
Route::controller('dashboard', 'DashboardController');

//Api Routes
Route::get('api', 'ApiController@getIndex');
    //Annotation Action Routes
    Route::post('api/annotations/{id}/likes', 'AnnotationApiController@postLikes')->where(array('id' => '[0-9a-zA-Z_-]+'));
    Route::post('api/annotations/{id}/dislikes', 'AnnotationApiController@postDislikes')->where(array('id' => '[0-9a-zA-Z_-]+'));
    Route::post('api/annotations/{id}/flags', 'AnnotationApiController@postFlags')->where(array('id' => '[0-9a-zA-Z_-]+'));
    Route::get('api/annotations/{id}/likes', 'AnnotationApiController@getLikes')->where(array('id' => '[0-9a-zA-Z_-]+'));
    Route::get('api/annotations/{id}/dislikes', 'AnnotationApiController@getDislikes')->where(array('id' => '[0-9a-zA-Z_-]+'));
    Route::get('api/annotations/{id}/flags', 'AnnotationApiController@getFlags')->where(array('id' => '[0-9a-zA-Z_-]+'));



    //Annotation Routes
    Route::get('api/annotations/search', 'AnnotationApiController@getSearch');
    Route::get('api/annotations', 'AnnotationApiController@getIndex');
    Route::post('api/annotations', 'AnnotationApiController@postIndex');
    Route::get('api/annotations/{id}', 'AnnotationApiController@getIndex')->where(array('id' => '[0-9a-zA-Z_-]+'));
    Route::put('api/annotations/{id}', 'AnnotationApiController@putIndex')->where(array('id' => '[0-9a-zA-Z_-]+'));
    Route::delete('api/annotations/{id}', 'AnnotationApiController@deleteIndex')->where(array('id' => '[0-9a-zA-Z_-]+'));





//Logout Route
Route::get('logout', function(){
	Auth::logout();	//Logout the current user
	Session::flush(); //delete the session
	return Redirect::to('/')->with('message', 'You have been successfully logged out.');
});

/**
*	Sitemap Route
*	TODO: What are the performance implications of this?  Are the results cached?  I would assume so, but not sure.
*/
Route::get('sitemap', function(){

	$sitemap = App::make('sitemap');

	$pages = array('about', 'faq', 'user/login', 'user/signup');

	foreach($pages as $page){
		$sitemap->add($page);
	}

    $docs = Doc::all();

    foreach ($docs as $doc)
    {
        $sitemap->add('doc/'.$doc->slug);
    }

    $notes = Note::all();

    foreach($notes as $note){
    	$sitemap->add('note/'.$note->id);
    }

    $users = User::all();

    foreach($users as $user){
    	$sitemap->add('user/'.$user->id);
    }

    // show your sitemap (options: 'xml' (default), 'html', 'txt', 'ror-rss', 'ror-rdf')
    return $sitemap->render('xml');
});


/*
|--------------------------------------------------------------------------
| Route Filters
|--------------------------------------------------------------------------
*/

/*
Route::post('register', array('before' => 'csrf', function()
{
    return 'You gave a valid CSRF token!';
}));
*/

Route::filter('auth', function()
{
	if (!Auth::check()) return Redirect::to('user/login');
});

Route::filter('admin', function(){
	if(Auth::guest() || Auth::user()->user_level != 1) return Redirect::home()->with('message', 'You are not authorized to view that page');
});
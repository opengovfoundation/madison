<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
*/

//Static Pages
Route::get('about', 'PageController@about');
Route::get('faq', 'PageController@faq');
Route::get('/', 'PageController@home');

//Document Routes
Route::get('docs', 'DocController@index');
Route::get('doc/{slug}', 'DocController@index');

//User Routes
Route::get('user/{id}', 'UserController@getIndex')->where(array('id' => '[0-9]+'));
Route::controller('user', 'UserController');

//Note Routes
Route::get('note/{id}', 'NoteController@getIndex')->where(array('id' => '[0-9]+'));
Route::post('note/{id}', 'NoteController@postIndex')->where(array('id' => '[0-9]+'));
Route::put('note/{id}', 'NoteController@putIndex')->where(array('id' => '[0-9]+'));
Route::controller('note', 'NoteController');

//Logout Route
Route::get('logout', function(){
	Auth::logout();	//Logout the current user
	Session::flush(); //delete the session
	return Redirect::to('/')->with('message', 'You have been successfully logged out.');
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
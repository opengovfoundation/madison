<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('about', 'PageController@about');
Route::get('faq', 'PageController@faq');
Route::get('/', 'PageController@home');

Route::any('doc/{slug}', 'DocController@index');

//Route::any('user/edit/(:num?)', 'UserController@edit');
//Route::any('user/(:num?)', 'UserController@index');

//Route::any('note/(:num?)', 'NoteController@index');
//Route::get('signup', 'LoginController@signup');
//Route::post('signup', 'LoginController@signup');
//Route::get('verify/(:num)/(:all)', 'LoginController@verify');

//Route::any('doc', function(){
//	return Redirect::to('docs');
//});
Route::any('docs', 'DocController@index');

//Route::get('logout', function(){
//	Auth::logout();	//Logout the current user
//	Session::flush(); //delete the session
//	return Redirect::back();
//});

/*
|--------------------------------------------------------------------------
| Route Filters
|--------------------------------------------------------------------------
*/

//Route::filter('csrf', function()
//{
//	if (Request::forged()) return Response::error('500');
//});

//Route::filter('auth', function()
//{
//	if (Auth::guest()) return Redirect::to('login');
//});

//Route::filter('admin', function(){
//	if(Auth::guest() || Auth::user()->user_level != 1) return Redirect::home()->with('message', 'You are not authorized to view that page');
//});
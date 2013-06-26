<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
*/

Route::get('about', 'page@about');
Route::get('faq', 'page@faq');
Route::get('/', 'page@home');

Route::any('doc/(:any?)', 'doc@index');
Route::any('note/(:num?)', 'note@index');
Route::get('signup', 'login@signup');
Route::post('signup', 'login@signup');
Route::get('verify/(:num)/(:all)', 'login@verify');

Route::any('doc', function(){
	return Redirect::to('docs');
});
Route::any('docs', 'doc@index');

Route::controller(Controller::detect());

Route::get('logout', function(){
	Auth::logout();	//Logout the current user
	Session::flush(); //delete the session
	return Redirect::to('home');
});

/*
|--------------------------------------------------------------------------
| Application 404 & 500 Error Handlers
|--------------------------------------------------------------------------
*/

Event::listen('404', function()
{
	return Response::error('404');
});

Event::listen('500', function()
{
	return Response::error('500');
});

/*
|--------------------------------------------------------------------------
| Route Filters
|--------------------------------------------------------------------------
*/

Route::filter('csrf', function()
{
	if (Request::forged()) return Response::error('500');
});

Route::filter('auth', function()
{
	if (Auth::guest()) return Redirect::to('login');
});

Route::filter('admin', function(){
	if(Auth::guest() || Auth::user()->user_level != 1) return Redirect::home()->with('message', 'You are not authorized to view that page');
});

<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// User Routes
Route::get('user/current', 'UserController@getCurrent');
Route::get('user/{user}/docs', 'DocumentController@getUserDocuments')->middleware(['auth']);

// Page Routes
Route::get('pages/', 'PageController@index');
Route::get('pages/{page}', 'PageController@show');
Route::post('pages/', 'PageController@store');
Route::put('pages/{page}', 'PageController@update');
Route::delete('pages/{page}', 'PageController@destroy');
Route::get('pages/{page}/content', 'PageController@getContent');
Route::put('pages/{page}/content', 'PageController@updateContent');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

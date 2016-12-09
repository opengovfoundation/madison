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
Route::get('user/current', 'Api\UserController@getCurrent');
Route::get('user/{user}/docs', 'Api\DocumentController@getUserDocuments')->middleware(['auth']);

// Page Routes
Route::get('pages/', 'Api\PageController@index');
Route::get('pages/{page}', 'Api\PageController@show');
Route::post('pages/', 'Api\PageController@store');
Route::put('pages/{page}', 'Api\PageController@update');
Route::delete('pages/{page}', 'Api\PageController@destroy');
Route::get('pages/{page}/content', 'Api\PageController@getContent');
Route::put('pages/{page}/content', 'Api\PageController@updateContent');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

<?php
/**
* Partial Routing File
*
* This file includes all page routes that have already been converted to the single-page-app-ness
*/

//Static Pages
Route::get('about', 'PageController@getAbout'); //TODO
Route::get('faq', 'PageController@faq'); //TODO
Route::get('privacy-policy', 'PageController@privacyPolicy'); //TODO
Route::get('terms-and-conditions', 'PageController@terms'); //TODO
Route::get('copyright', 'PageController@copyright'); //TODO

Route::get('user/edit/{user}/notifications', 'UserController@editNotifications');
Route::get('/', array('as' => 'home', 'uses' => 'PageController@home'));
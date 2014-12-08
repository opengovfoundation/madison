<?php
/**
* Partial Routing File
*
* This file includes all page routes that have already been converted to the single-page-app-ness
*/

Route::get('user/edit/{user}/notifications', 'UserController@editNotifications');
Route::get('/', array('as' => 'home', 'uses' => 'PageController@home'));
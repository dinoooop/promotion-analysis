<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('test', 'TestController@test');
Route::get('local-test', 'TestController@local_test');
Route::any('admin', 'UsersController@login');
Route::get('admin/logout', 'UsersController@logout');
Route::get('admin/profile', 'UsersController@profile');


Route::group([ 'prefix' => 'admin', 'middleware' => 'role'], function() {
    Route::resource('users', 'UsersController');
    Route::resource('promotions', 'PromotionsController');
    Route::resource('items', 'ItemsController');
    
    Route::get('ajax/promotion-status/{id}/{status}', 'PromotionsController@update_promotion_status');
});


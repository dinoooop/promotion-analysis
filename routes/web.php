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
    Route::resource('multiples', 'MultiplesController');
    Route::resource('configurations', 'ConfigurationsController');
    Route::resource('results', 'ResultsController');

    Route::get('promotion-multiple', 'PromotionsController@promotion_multiple');

    Route::get('ajax/promotion-status/{id}/{status}', 'PromotionsController@update_promotion_status');
    Route::get('ajax', 'AjaxController@index');
    Route::post('submit_promotion_multiple', 'PromotionsController@submit_promotion_multiple');


    Route::get('prepare-promotion', 'PagesController@prepare_result')->name('prepare_promotion');
    Route::get('preparation_table', 'ResultsController@preparation_table')->name('preparation_table');
    Route::get('items/create-kendo', 'ItemsController@create_kendo');
});

Route::get('kendo/promotions', 'PromotionsController@kendo_index');
Route::get('kendo/items', 'ItemsController@kendo_index');
Route::get('kendo/preparation-table', 'ResultsController@kendo_preparation_table')->name('kendo_preparation_table');

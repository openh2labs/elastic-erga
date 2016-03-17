<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

use App\Post;

//show the overall dashboard
Route::get('/', 'AlertController@home');

Route::get('user/{id}', 'UserController@showProfile');

Route::get('alert/home/{state}', 'AlertController@home');

Route::get('alert/home/', 'AlertController@home');

Route::get('alertrun/systemlog', 'SystemLogController@home');

Route::get('terminal', 'TerminalController@index');

Route::get('terminal/show', 'TerminalController@show');


//execute searches
Route::get('alert/searchtest', 'AlertController@searchtest');

//create test index
Route::get('alert/createTestIndex', 'AlertController@createTestIndex');

/*
 * alert management
 */

//create an alert
Route::get('alert/createnew', 'AlertMgtController@create');

//store a new alert
Route::post('alert/store', 'AlertMgtController@store');

//edit an alert
Route::get('alert/edit/{id}', 'AlertMgtController@edit');

//store an alert edit
Route::post('alert/storeedit/{id}', 'AlertMgtController@storeedit');

/*
 * alert executios
 */
Route::get('alertexecutions/purge/', 'AlertExecutionController@purge');

/*
 * Librato
 */
Route::get('librato/create/{alert_id}', 'LibratoMgt@create');

//store a librato edit
Route::post('librato/store/{alert_id}', 'LibratoMgt@store');

Route::get('librato/edit/{alert_id}', 'LibratoMgt@edit');

/*
 * typeAhead routes
 */
Route::get('typeahead/listcolumn/{column}/{table}', 'TypeAhead@listcolumn');



/**
 * Search Routes
 */
Route::get('/search', 'SearchController@elastic');
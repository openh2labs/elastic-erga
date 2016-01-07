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

Route::get('/', 'AlertController@home');

Route::get('user/{id}', 'UserController@showProfile');

Route::get('alert/home/{state}', 'AlertController@home');

Route::get('alert/home/', 'AlertController@home');

Route::get('alertrun/systemlog', 'SystemLogController@home');

//execute searches @todo create cron
Route::get('alert/searchtest', 'AlertController@searchtest');

//create test index
Route::get('alert/createTestIndex', 'AlertController@createTestIndex');


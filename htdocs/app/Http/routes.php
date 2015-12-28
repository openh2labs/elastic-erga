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

//use App\Http\Controllers\PostController;
use App\Post;


//namespace App;

Route::get('/', function () {
    return view('welcome');
});

Route::get('user/{id}', 'UserController@showProfile');

Route::get('alert/home', 'AlertController@home');

//execute searches @todo create cron
Route::get('alert/searchtest', 'AlertController@searchtest');

//create test index
Route::get('alert/createTestIndex', 'AlertController@createTestIndex');

Route::get('/', ['as' => 'search', 'uses' => function() {

    // Check if user has sent a search query
    if($query = Input::get('query', false)) {
        // Use the Elasticquent search method to search ElasticSearch
        $posts = Post::search($query);
    } else {
        // Show all posts if no query is set
       // $t = new App/PostController;
        $posts = Post::all();
    }

    return View::make('home', compact('posts'));

}]);
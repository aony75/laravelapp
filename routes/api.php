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

Route::middleware('auth:api')->get('user', function (Request $request) {
    return $request->user();
});

//all api routes in this group must use access token to work
Route::middleware('auth:api')->group(function(){   

    //list people 
    Route::get('people', 'PeopleController@index');

    //create new person 
    Route::post('people', 'PeopleController@index');

    //list single person 
    Route::get('people/{id}', 'PeopleController@show');

    //create new person 
    Route::post('people', 'PeopleController@store');

    //uodate person 
    Route::put('people', 'PeopleController@store');

    //delete person 
    Route::delete('people/{id}', 'PeopleController@destroy');
        
});


<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//if user goes to index login form shows
Route::get('/', function(){
    return view('auth.login');
})->middleware('guest');

Auth::routes();

//Error occurs with get request. Blocking get request to logout and password email with middleware
Route::middleware('guest', 'auth')->group(function(){   
    Route::get('/logout');
    Route::get('password/email');
});

Route::get('/home', 'HomeController@index')->name('home');

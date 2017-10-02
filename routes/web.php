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



Auth::routes();

Route::group(['middleware' => 'auth'], function () {

	Route::get('/', 'HomeController@index')->name('home');

	// Pads
	Route::post('/pad/create', 'PadController@create')->name('create');
	Route::post('/pad/archive', 'PadController@archive')->name('archive');
	Route::post('/pad/delete', 'PadController@delete')->name('delete');
	
	Route::get('/pad/info/{slug}', 'PadController@info')->name('info');
	
});



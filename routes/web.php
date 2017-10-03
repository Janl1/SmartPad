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

	//ACP
	Route::get('/acp/user/info/{id}', 'AcpController@userInfo')->name('userInfo');
	Route::post('/acp/user/edit/{id}', 'AcpController@userEdit')->name('userEdit');

	Route::get('/acp/users', 'AcpController@users')->name('users');
	Route::get('/acp/logs', 'AcpController@logs')->name('logs');
	Route::get('/acp/trash', 'AcpController@trash')->name('trash');

	// Pads
	Route::post('/pad/create', 'PadController@create')->name('create');
	Route::post('/pad/archive', 'PadController@archive')->name('archive');
	Route::post('/pad/delete', 'PadController@delete')->name('delete');
	
	Route::get('/pad/info/{slug}', 'PadController@info')->name('info');
	Route::get('/pad/open/{slug}', 'PadController@open')->name('open');
	Route::get('/pad/password/{slug}', 'PadController@password')->name('password');
	Route::post('/pad/password/{slug}', 'PadController@password')->name('password');
	
});



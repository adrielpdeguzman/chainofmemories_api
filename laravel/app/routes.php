<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::group(array('prefix' => 'api/v1'), function()
{
    Route::resource('users', 'UserController');

    Route::group(array('prefix' => 'journals'), function()
    {
        Route::get('random', 'JournalController@random');
        Route::get('volume/{volume}', 'JournalController@volume');
    });
    Route::resource('journals', 'JournalController');
});

Route::get('/', function()
{
	return View::make('hello');
});

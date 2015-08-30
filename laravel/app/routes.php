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

Route::group(['prefix' => 'api/v1'], function()
{
    Route::post('oauth/access_token', function() {
        return Response::json(Authorizer::issueAccessToken(), 200);
    });

    Route::group(['before' => 'oauth'], function()
    {
        Route::resource('users', 'UserController', ['except' => ['create', 'edit']]);

        // Route::group(['before' => 'basic.once'], function()
        // {
            Route::group(['prefix' => 'journals'], function()
            {
                Route::get('random', ['uses' => 'JournalController@random', 'as' => 'api.v1.journals.random']);
                Route::get('search', ['uses' => 'JournalController@search', 'as' => 'api.v1.journals.search']);
                Route::get('volume/{volume}', ['uses' => 'JournalController@volume', 'as' => 'api.v1.journals.volume']);
                Route::get('getDatesWithoutEntry', ['uses' => 'JournalController@getDatesWithoutEntry', 'as' => 'api.v1.journals.dates_without_entry']);
            });
            Route::resource('journals', 'JournalController', ['except' => ['create', 'edit']]);
        // });

        Route::post('sessions', ['uses' => 'SessionController@store', 'as' => 'api.v1.sessions.login']);
        // Route::delete('sessions', ['uses' => 'SessionController@destroy', 'as' => 'api.v1.sessions.logout']);
    });
});

Route::get('/', function()
{
	return View::make('hello');
});

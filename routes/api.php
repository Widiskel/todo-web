<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['prefix' => 'auth'],function () {
    Route::post('login', 'ApiAuthController@login');
    Route::post('register', 'ApiAuthController@register');
    Route::get('logout', 'ApiAuthController@logout')->middleware('auth:sanctum');
});

Route::group(['prefix' => 'task','middleware' => 'auth:sanctum'],function () {
    Route::get('/', 'ApiTaskController@get_task');
    Route::get('/all', 'ApiTaskController@get_all_task');
    Route::get('/done/{task}', 'ApiTaskController@done_task');
    Route::post('/create', 'ApiTaskController@add_task');
    Route::post('/edit/{task}', 'ApiTaskController@edit_task');
    Route::delete('/{task}', 'ApiTaskController@delete_task');
});
Route::group(['prefix' => 'daily-task','middleware' => 'auth:sanctum'],function () {
    Route::get('/', 'ApiDailyTaskController@get_daily_task');
    Route::get('/done/{task}', 'ApiDailyTaskController@done_task');
});



<?php

use App\Http\Controllers\GraphController;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/graph', 'GraphController@view');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/graph', 'GraphController@view');

Route::post('/graph', 'GraphController@view');

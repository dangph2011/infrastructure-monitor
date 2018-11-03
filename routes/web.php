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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/graph', 'GraphController@view');

Route::post('/graph', 'GraphController@view');

Route::get('/graph/download/{data}/{layout}', [
    'uses' => 'GraphController@download',
    'as'   => '/graph/download'
]);

Route::get('/problem', 'ProblemController@view');

Route::post('/problem', 'ProblemController@view');

Route::get('/trigger', 'TriggerController@view');

Route::post('/trigger', 'TriggerController@view');

Route::get('/trigger/comments', 'TriggerController@comment');

Route::post('/trigger/comments', 'TriggerController@comment');

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/graph', 'GraphController@view');

Route::get('/graph/create', 'GraphController@create');

Route::post('/graph/create', 'GraphController@store');

Route::get('/graph/download/{data}/{layout}', [
    'uses' => 'GraphController@download',
    'as'   => '/graph/download'
]);

Route::get('/problems/problem', 'ProblemController@view');

Route::get('/problems/trigger', 'TriggerController@view');

Route::get('/problems/trigger/create', 'TriggerController@create');

Route::post('/problems/trigger/create', 'TriggerController@store');

Route::get('problems/trigger/comments', 'TriggerController@comment');

Route::post('problems/trigger/comments', 'TriggerController@comment');

// Route::get('/report', 'ReportController@view');

Route::resource('report', 'ReportController');

Route::get('/ajax/group', 'AjaxController@ajaxGetGroup');
Route::get('/ajax/host', 'AjaxController@ajaxGetHostByGroupId');
Route::get('/ajax/graph', 'AjaxController@ajaxGetGraphByGroupAndHost');
Route::get('/ajax/chart/graph', 'AjaxController@ajaxGetChartValueByGraphId');
Route::get('/ajax/chart/item', 'AjaxController@ajaxGetChartValueByItem');
Route::get('/ajax/chart/range', 'AjaxController@ajaxGetRangeValue');

// Route::get('/report/create', 'ReportController@create');

// Route::post('/report/create', 'ReportController@store');
Route::get('replication/history', 'ReplicationController@history');
Route::resource('replication', 'ReplicationController');
Route::resource('dump', 'DumpHistoryController');
Route::resource('local', 'LocalServerController');

Route::get('/home', 'HomeController@index')->name('home');


Route::group(['middleware' => ['auth']], function() {
    Route::resource('roles','RoleController');
    Route::resource('users','UserController');
});


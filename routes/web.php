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

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/jsonFile', 'Report\\ReportsController@jsonFile');
Route::post('/report/camera_reports', 'Report\\ReportsController@storeFromCamera');
Route::post('/report/storePersonWithSuspect', 'Report\\ReportsController@storePersonWithSuspect');
Route::get('/report/getStamp/{id}', 'Report\\ReportsController@getStamp');
Route::get('/report/getStamps', 'Report\\ReportsController@getStamps');
Route::get('/report/getReport/{id}', 'Report\\ReportsController@getReport');
Route::get('/report/getMatchingReports/{id}', 'Match\\MatchesController@getMatchingReports');
Route::get('/areas/areasRating/{id}', 'Area\\AreasController@areasRating');
Route::get('/cities/citiesRating', 'City\\CitiesController@citiesRating');


Route::resource('report/reports', 'Report\\ReportsController');
Route::resource('stamp/stamps', 'Stamp\\StampsController');
Route::resource('suspect/suspects', 'Suspect\\SuspectsController');
Route::resource('city/cities', 'City\\CitiesController');
Route::resource('area/areas', 'Area\\AreasController');
Route::resource('match/matches', 'Match\\MatchesController');
Route::resource('user/users', 'UsersController');

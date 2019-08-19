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

Route::get('/', ['as' => 'pi', 'uses' => 'PiController@index', 'middleware' => 'auth']);

/*Route::resources([
    'pis' => 'PiController',
    'users' => 'UserController',
    'maps' => 'MapController',
]);*/

Route::resource('pis', 'PiController')->middleware('auth');
Route::resource('users', 'UserController')->middleware('auth');
Route::resource('maps', 'MapController')->middleware('auth');

Auth::routes();

//Route::get('/home', 'HomeController@index')->name('home');
Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');

Route::post('pis/storePi', 'PiController@storePi');
Route::post('pis/deletePi', 'PiController@deletePi');
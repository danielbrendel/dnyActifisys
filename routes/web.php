<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', 'MainController@index');
Route::get('/faq', 'MainController@faq');
Route::get('/imprint', 'MainController@imprint');
Route::get('/news', 'MainController@news');
Route::get('/contact', 'MainController@viewContact');
Route::get('/tos', 'MainController@tos');
Route::post('/register', 'MainController@register');
Route::post('/reset', 'MainController@reset');
Route::get('/confirm', 'MainController@confirm');
Route::post('/login', 'MainController@login');
Route::any('/logout', 'MainController@logout');

Route::post('/activity/create', 'ActivityController@create');
Route::get('/activity/{id}', 'ActivityController@show');
Route::get('/activity/{id}/thread', 'ActivityController@fetchThread');
Route::post('/activity/{id}/thread/add', 'ActivityController@addThread');
Route::post('/thread/{parentId}/reply', 'ActivityController@replyThread');
Route::get('/thread/{parentId}/sub', 'ActivityController@fetchSubThread');

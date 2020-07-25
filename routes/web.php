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
Route::get('/activity/fetch', 'ActivityController@fetch');
Route::get('/activity/{id}', 'ActivityController@show');
Route::get('/activity/{id}/thread', 'ActivityController@fetchThread');
Route::post('/activity/{id}/thread/add', 'ActivityController@addThread');
Route::post('/thread/{parentId}/reply', 'ActivityController@replyThread');
Route::get('/thread/{parentId}/sub', 'ActivityController@fetchSubThread');
Route::get('/activity/{activityId}/participant/add', 'ActivityController@participantAdd');
Route::get('/activity/{activityId}/participant/remove', 'ActivityController@participantRemove');
Route::get('/activity/{activityId}/interested/add', 'ActivityController@potentialAdd');
Route::get('/activity/{activityId}/interested/remove', 'ActivityController@potentialRemove');
Route::any('/activity/{activityId}/cancel', 'ActivityController@cancelActivity');
Route::any('/activity/{activityId}/lock', 'ActivityController@lockActivity');
Route::any('/activity/{activityId}/report', 'ActivityController@reportActivity');
Route::any('/comment/{id}/lock', 'ActivityController@lockComment');
Route::any('/comment/{id}/report', 'ActivityController@reportComment');
Route::post('/comment/{id}/edit', 'ActivityController@editComment');
Route::get('/activity/user/{id}', 'ActivityController@fetchUserActivities');

Route::get('/user/{id}', 'MemberController@show');
Route::get('/user/{id}/fav/add', 'FavoritesController@add');
Route::get('/user/{id}/fav/remove', 'FavoritesController@add');
Route::get('/user/{id}/lock', 'MemberController@lock');
Route::get('/user/{id}/report', 'MemberController@report');
Route::get('/user/{id}/ignore/add', 'MemberController@ignoreAdd');
Route::get('/user/{id}/ignore/remove', 'MemberController@ignoreRemove');
Route::get('/settings', 'MemberController@viewSettings');
Route::post('/settings/password', 'MemberController@savePassword');
Route::post('/settings/email', 'MemberController@saveEMail');
Route::post('/settings/notifications', 'MemberController@saveNotifications');
Route::post('/settings/delete', 'MemberController@deleteUser');

Route::get('/notifications/list', 'NotificationController@list');
Route::get('/notifications/fetch', 'NotificationController@fetch');

Route::get('/messages', 'MessageController@list');
Route::get('/messages/list', 'MessageController@fetchList');
Route::get('/messages/show/{id}', 'MessageController@show');
Route::get('/messages/create', 'MessageController@create');
Route::post('/messages/send', 'MessageController@send');

Route::get('/install', 'InstallerController@viewInstall');
Route::post('/install', 'InstallerController@install');

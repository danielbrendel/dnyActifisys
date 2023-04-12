<?php

/*
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2023 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

use Illuminate\Support\Facades\Route;

Route::get('/', 'MainController@index');
Route::get('/faq', 'MainController@faq');
Route::get('/imprint', 'MainController@imprint');
Route::get('/news', 'MainController@news');
Route::get('/contact', 'MainController@viewContact');
Route::post('/contact', 'MainController@contact');
Route::get('/tos', 'MainController@tos');
Route::post('/register', 'MainController@register');
Route::get('/resend/{userId}', 'MainController@resend');
Route::post('/recover', 'MainController@recover');
Route::post('/reset', 'MainController@reset');
Route::get('/reset', 'MainController@viewReset');
Route::get('/confirm', 'MainController@confirm');
Route::post('/login', 'MainController@login');
Route::any('/logout', 'MainController@logout');
Route::get('/clep/index', 'MainController@clep');
Route::get('/sitemap', 'MainController@sitemap');

Route::post('/activity/create', 'ActivityController@create');
Route::post('/activity/edit', 'ActivityController@edit');
Route::get('/activity/fetch', 'ActivityController@fetch');
Route::get('/activity/{slugOrId}', 'ActivityController@show');
Route::get('/activity/{slugOrId}/refresh', 'ActivityController@refresh');
Route::get('/activity/{id}/thread', 'ActivityController@fetchThread');
Route::post('/activity/{id}/thread/add', 'ActivityController@addThread');
Route::post('/thread/{parentId}/reply', 'ActivityController@replyThread');
Route::get('/thread/{parentId}/sub', 'ActivityController@fetchSubThread');
Route::get('/activity/{activityId}/participant/add', 'ActivityController@participantAdd');
Route::get('/activity/{activityId}/participant/remove', 'ActivityController@participantRemove');
Route::get('/activity/{activityId}/interested/add', 'ActivityController@potentialAdd');
Route::get('/activity/{activityId}/interested/remove', 'ActivityController@potentialRemove');
Route::post('/activity/{activityId}/cancel', 'ActivityController@cancelActivity');
Route::any('/activity/{activityId}/lock', 'ActivityController@lockActivity');
Route::any('/activity/{activityId}/report', 'ActivityController@reportActivity');
Route::any('/comment/{id}/lock', 'ActivityController@lockComment');
Route::any('/comment/{id}/report', 'ActivityController@reportComment');
Route::post('/comment/{id}/edit', 'ActivityController@editComment');
Route::any('/activity/user/{id}', 'ActivityController@fetchUserActivities');
Route::get('/activity/user/{id}/participations', 'ActivityController@fetchUserParticipations');
Route::post('/activity/{id}/upload', 'ActivityController@uploadFile');
Route::any('/file/{id}/delete', 'ActivityController@deleteFile');
Route::get('/locations/query', 'ActivityController@queryLocation');
Route::get('/activity/reminder/{password}', 'ActivityController@reminderJob');

Route::get('/user/{slugOrId}', 'MemberController@show');
Route::get('/user/{id}/fav/add', 'FavoritesController@add');
Route::get('/user/{id}/fav/remove', 'FavoritesController@remove');
Route::get('/user/{id}/lock', 'MemberController@lock');
Route::get('/user/{id}/report', 'MemberController@report');
Route::get('/user/{id}/ignore/add', 'MemberController@ignoreAdd');
Route::get('/user/{id}/ignore/remove', 'MemberController@ignoreRemove');
Route::get('/user/device/token', 'MemberController@setDeviceToken');
Route::get('/settings', 'MemberController@viewSettings');
Route::post('/settings', 'MemberController@saveSettings');
Route::post('/settings/avatar', 'MemberController@saveAvatar');
Route::post('/settings/password', 'MemberController@savePassword');
Route::post('/settings/email', 'MemberController@saveEMail');
Route::post('/settings/notifications', 'MemberController@saveNotifications');
Route::post('/settings/verify', 'MemberController@verifyAccount');
Route::post('/settings/delete', 'MemberController@deleteUser');
Route::post('/settings/privacy/publicprofile', 'MemberController@setPublicProfileValue');
Route::post('/settings/privacy/allowmessages', 'MemberController@setAllowMessagesValue');
Route::get('/favorites/fetch', 'FavoritesController@fetch');

Route::get('/notifications/list', 'NotificationController@list');
Route::get('/notifications/fetch', 'NotificationController@fetch');
Route::get('/notifications/seen', 'NotificationController@seen');

Route::get('/messages', 'MessageController@list');
Route::get('/messages/list', 'MessageController@fetchList');
Route::get('/messages/show/{id}', 'MessageController@show');
Route::any('/messages/query', 'MessageController@query');
Route::get('/messages/create', 'MessageController@create');
Route::post('/messages/send', 'MessageController@send');
Route::post('/messages/image', 'MessageController@image');

Route::get('/forum', 'ForumController@index');
Route::post('/forum/list', 'ForumController@list');
Route::get('/forum/{id}/show', 'ForumController@show');
Route::post('/forum/{id}/list', 'ForumController@threadList');
Route::get('/forum/thread/{id}/show', 'ForumController@showThread');
Route::post('/forum/thread/{id}/posts', 'ForumController@threadPostings');
Route::post('/forum/thread/create', 'ForumController@createThread');
Route::post('/forum/thread/reply', 'ForumController@replyThread');
Route::post('/forum/thread/edit', 'ForumController@editThread');
Route::get('/forum/thread/post/{id}/show', 'ForumController@showPost');
Route::any('/forum/thread/post/{id}/report', 'ForumController@reportPost');
Route::any('/forum/thread/post/{id}/lock', 'ForumController@lockPost');
Route::post('/forum/thread/post/edit', 'ForumController@editPost');

Route::get('/gallery', 'GalleryController@index');
Route::post('/gallery/fetch', 'GalleryController@fetch');
Route::post('/gallery/add', 'GalleryController@add');
Route::any('/gallery/{id}/like', 'GalleryController@like');
Route::any('/gallery/{id}/remove', 'GalleryController@remove');
Route::any('/gallery/{id}/report', 'GalleryController@report');
Route::get('/gallery/item/{slug}', 'GalleryController@view');
Route::post('/gallery/thread/add', 'GalleryController@addThread');

Route::get('/marketplace', 'MarketplaceController@index');
Route::post('/marketplace/list', 'MarketplaceController@list');
Route::post('/marketplace/create', 'MarketplaceController@create');
Route::post('/marketplace/{id}/edit', 'MarketplaceController@edit');
Route::any('/marketplace/{id}/delete', 'MarketplaceController@delete');
Route::any('/marketplace/{id}/report', 'MarketplaceController@report');

Route::get('/maintainer', 'MaintainerController@index');
Route::post('/maintainer/save', 'MaintainerController@save');
Route::post('/maintainer/about', 'MaintainerController@about');
Route::post('/maintainer/faq/create', 'MaintainerController@addFaq');
Route::post('/maintainer/faq/edit', 'MaintainerController@editFaq');
Route::get('/maintainer/faq/{id}/remove', 'MaintainerController@removeFaq');
Route::post('/maintainer/env/save', 'MaintainerController@envSave');
Route::get('/maintainer/u/details', 'MaintainerController@userDetails');
Route::post('/maintainer/u/save', 'MaintainerController@userSave');
Route::post('/maintainer/newsletter', 'MaintainerController@newsletter');
Route::post('/maintainer/themes/add', 'MaintainerController@addTheme');
Route::post('/maintainer/themes/edit', 'MaintainerController@editTheme');
Route::get('/maintainer/themes/delete', 'MaintainerController@deleteTheme');
Route::get('/maintainer/themes/setdefault', 'MaintainerController@setDefaultTheme');
Route::post('/maintainer/logo/save', 'MaintainerController@saveLogo');
Route::post('/maintainer/background/save', 'MaintainerController@saveBackground');
Route::get('/maintainer/entity/lock', 'MaintainerController@lockEntity');
Route::get('/maintainer/entity/delete', 'MaintainerController@deleteEntity');
Route::get('/maintainer/entity/safe', 'MaintainerController@setSafeEntity');
Route::post('/maintainer/welcomecontent', 'MaintainerController@welcomeContent');
Route::post('/maintainer/formattedprojectname', 'MaintainerController@saveFormattedProjectName');
Route::get('/maintainer/verify/{id}/approve', 'MaintainerController@approveAccount');
Route::get('/maintainer/verify/{id}/decline', 'MaintainerController@declineAccount');
Route::post('/maintainer/category/add', 'MaintainerController@addCategory');
Route::post('/maintainer/category/{id}/edit', 'MaintainerController@editCategory');
Route::any('/maintainer/category/{id}/inactive/{status}', 'MaintainerController@categoryInactiveStatus');
Route::post('/maintainer/location/add', 'MaintainerController@addLocation');
Route::post('/maintainer/location/{id}/edit', 'MaintainerController@editLocation');
Route::any('/maintainer/location/{id}/active/{status}', 'MaintainerController@locationActiveStatus');
Route::post('/maintainer/announcements/create', 'MaintainerController@createAnnouncement');
Route::post('/maintainer/forum/create', 'MaintainerController@createForum');
Route::post('/maintainer/forum/edit', 'MaintainerController@editForum');
Route::any('/maintainer/forum/{id}/lock', 'MaintainerController@lockForum');
Route::any('/maintainer/forum/{id}/remove', 'MaintainerController@removeForum');

Route::post('/payment/charge', 'PaymentController@charge');

Route::get('/install', 'InstallerController@viewInstall');
Route::post('/install', 'InstallerController@install');

Route::any('/newsletter_cronjob/{password}', 'MainController@newsletter_cronjob');
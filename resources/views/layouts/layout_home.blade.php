{{--
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2023 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', App::getLocale()) }}">
    @include('layouts.layout_head')    

    <body style="background-image: url('{{ asset('gfx/backgrounds/' . \App\AppModel::getHomeBackground()) }}');">
        <div id="app" style="background-color: rgba(0, 0, 0, {{ \App\AppModel::getHomeBackgroundAlpha() }});">
            <nav class="navbar" role="navigation" aria-label="main navigation">
                <div class="navbar-brand">
                    <a class="navbar-item" href="{{ url('/') }}">
                        @if (strlen(\App\AppModel::getFormattedProjectName()) > 0)
                            {!! \App\AppModel::getFormattedProjectName() !!}
                        @else
                            <strong>{{ env('APP_PROJECTNAME') }}</strong>
                        @endif
                    </a>

                    @if (\App\User::isMaintainer(auth()->id()))
                        <div class="is-pointer is-fixed-maintainer-icon" onclick="location.href = '{{ url('/maintainer') }}';"><i class="fas fa-cog"></i></div>
                    @endif
                </div>


                <a id="navbarBurger" role="button" class="navbar-burger burger" aria-label="menu" aria-expanded="false" data-target="navbarMenu" onclick="window.menuVisible = !document.getElementById('navbarMenu').classList.contains('is-active');">
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                    <span id="burger-notification"></span>
                </a>

                <div id="navbarMenu" class="navbar-menu">
                <div class="navbar-start">
                </div>

                <div class="navbar-end">
                    @auth
                        <a class="button navbar-item fix-mobile-navbar-item is-success navbar-fixed-button-top is-uppercase" href="javascript:void(0);" onclick="window.vue.bShowCreateActivity = true; document.getElementById('btnCreateActivity').disabled = true; if (window.menuVisible) { document.getElementById('navbarMenu').classList.remove('is-active'); document.getElementById('navbarBurger').classList.remove('is-active'); }">
                            {{ __('app.create_activity') }}
                        </a>

                        <a class="navbar-item fix-mobile-navbar-item is-white" href="javascript:void(0);" onclick="window.vue.toggleFavorites('favorites'); if (window.menuVisible) { document.getElementById('navbarMenu').classList.remove('is-active'); document.getElementById('navbarBurger').classList.remove('is-active'); }">
                            {{ __('app.favorites') }}
                        </a>

                        <a class="navbar-item fix-mobile-navbar-item-mobile notification-badge" href="javascript:void(0);" onclick="window.vue.toggleNotifications('notifications'); document.getElementById('navbar-notify-wrapper').classList.add('is-hidden'); document.getElementById('burger-notification').style.display = 'none'; window.vue.markSeen(); if (window.menuVisible) { document.getElementById('navbarMenu').classList.remove('is-active'); document.getElementById('navbarBurger').classList.remove('is-active'); }">
                            <span>{{ __('app.notifications') }}</span>
                            <span class="notify-badge is-hidden" id="navbar-notify-wrapper"><span class="notify-badge-count" id="navbar-notify-count"></span></span>
                        </a>

                        <a class="navbar-item fix-mobile-navbar-item is-white" href="{{ url('/messages') }}">
                            {{ __('app.messages') }}
                        </a>

                        <div class="navbar-item fix-mobile-navbar-item has-dropdown is-hoverable">
							<a class="navbar-link">
								{{ __('app.community') }}
							</a>

							<div class="navbar-dropdown">
                                @if (env('APP_ENABLEGALLERY'))
                                <a class="navbar-item fix-mobile-navbar-item is-white" href="{{ url('/gallery') }}">
                                    {{ __('app.gallery') }}
                                </a>
                                @endif

                                <a class="navbar-item fix-mobile-navbar-item is-white" href="{{ url('/forum') }}">
                                    {{ __('app.forum') }}
                                </a>

                                @if (env('APP_ENABLEMARKETPLACE'))
                                <a class="navbar-item fix-mobile-navbar-item is-white" href="{{ url('/marketplace') }}">
                                    {{ __('app.marketplace') }}
                                </a>
                                @endif
							</div>
						</div>

                        <a class="navbar-item fix-mobile-navbar-item is-white" href="{{ url('/user/' . \App\User::get(auth()->id())->slug) }}">
                            {{ __('app.profile') }}
                        </a>

                        <a class="navbar-item fix-mobile-navbar-item is-white" href="{{ url('/settings') }}">
                            {{ __('app.settings') }}
                        </a>

                        <a class="navbar-item fix-mobile-navbar-item is-white" href="{{ url('/logout') }}">
                            {{ __('app.logout') }}
                        </a>
                    @endauth

                    @guest
                        <div class="navbar-item">
                            <div class="buttons">
                                <a class="button is-success is-uppercase" href="javascript:void(0);" onclick="vue.bShowLogin = true;">
                                    {{ __('app.create_activity') }}
                                </a>
                                &nbsp;&nbsp;
                                <a class="navbar-inline-item" href="{{ url('/forum') }}">
                                    {{ __('app.forum') }}
                                </a>
                                &nbsp;&nbsp;
                                @if (env('APP_ENABLEGALLERY'))
                                <a class="navbar-inline-item" href="{{ url('/gallery') }}">
                                    {{ __('app.gallery') }}
                                </a>
                                &nbsp;&nbsp;
                                @endif
                                @if (env('APP_ENABLEMARKETPLACE'))
                                <a class="navbar-inline-item" href="{{ url('/marketplace') }}">
                                    {{ __('app.marketplace') }}
                                </a>
                                &nbsp;&nbsp;
                                @endif
                                <a class="button is-light is-bold is-outlined" href="javascript:void(0);" onclick="vue.bShowRegister = true;">
                                    {{ __('app.register') }}
                                </a>
                                &nbsp;&nbsp;
                                <a class="navbar-inline-item" href="javascript:void(0);" onclick="vue.bShowLogin = true;">
                                    {{ __('app.login') }}
                                </a>
                            </div>
                        </div>
                    @endguest
                </div>
                </div>
            </nav>

            @if ($errors->any())
                <div id="error-message-1">
                    <article class="message is-danger">
                        <div class="message-header">
                            <p>{{ __('app.error') }}</p>
                            <button class="delete" aria-label="delete" onclick="document.getElementById('error-message-1').style.display = 'none';"></button>
                        </div>
                        <div class="message-body">
                            @foreach ($errors->all() as $error)
                                {{ $error }}<br/>
                            @endforeach
                        </div>
                    </article>
                </div>
                <br/>
            @endif

            @if (Session::has('error'))
                <div id="error-message-2">
                    <article class="message is-danger">
                        <div class="message-header">
                            <p>{{ __('app.error') }}</p>
                            <button class="delete" aria-label="delete" onclick="document.getElementById('error-message-2').style.display = 'none';"></button>
                        </div>
                        <div class="message-body">
                            {{ Session::get('error') }}
                        </div>
                    </article>
                </div>
                <br/>
            @endif

            @if (Session::has('success'))
                <div id="success-message">
                    <article class="message is-success">
                        <div class="message-header">
                            <p>{{ __('app.success') }}</p>
                            <button class="delete" aria-label="delete" onclick="document.getElementById('success-message').style.display = 'none';"></button>
                        </div>
                        <div class="message-body">
                            {!! Session::get('success') !!}
                        </div>
                    </article>
                </div>
                <br/>
            @endif

            <div class="flash is-flash-error" id="flash-error">
                <p id="flash-error-content">
                    @if (Session::has('flash.error'))
                        {{ Session::get('flash.error') }}
                    @endif
                </p>
            </div>

            <div class="flash is-flash-success" id="flash-success">
                <p id="flash-success-content">
                    @if (Session::has('flash.success'))
                        {{ Session::get('flash.success') }}
                    @endif
                </p>
            </div>

            @yield('announcements')

            @include('widgets.header')

            @guest
                @if (env('APP_ABOUTPOSITION', 'bottom') === 'top')
                    <div class="overflow-hidden">
                        @include('widgets.about', ['about_content' => \App\AppModel::getAboutContent()])
                    </div>
                @endif
            @endguest

            <div class="container">
                <div class="notifications @if ((!isset($show_header)) || ($show_header == false)) {{ 'notifications-no-header' }} @endif" id="notifications">
                    <center><div class="notifications-arrow-up"></div></center>

                    <div>
                        <div class="is-inline-block"></div>
                        <div class="is-inline-block float-right notification-close-action is-pointer" onclick="window.vue.toggleNotifications('notifications'); if (window.menuVisible) {document.getElementById('navbarMenu').classList.remove('is-active'); document.getElementById('navbarBurger').classList.remove('is-active'); }">{{ __('app.close') }}</div>
                    </div>

                    <div class="notifications-content" id="notification-content"></div>
                </div>

                <div class="favorites @if ((!isset($show_header)) || ($show_header == false)) {{ 'notifications-no-header' }} @endif" id="favorites">
                    <center><div class="favorites-arrow-up"></div></center>

                    <div>
                        <div class="is-inline-block"></div>
                        <div class="is-inline-block float-right favorites-close-action is-pointer" onclick="window.vue.toggleFavorites('favorites'); if (window.menuVisible) {document.getElementById('navbarMenu').classList.remove('is-active'); document.getElementById('navbarBurger').classList.remove('is-active'); }">{{ __('app.close') }}</div>
                    </div>

                    <div class="favorites-content" id="favorites-act-content"><i class="fa fa-spinner fa-spin"></i></div>
                    <div class="favorites-content" id="favorites-content"><i class="fa fa-spinner fa-spin"></i></div>
                </div>
                
                <div class="columns">
                    @yield('content')
                </div>

                <div class="cookie-consent-outer">
                    <div id="cookie-consent" class="cookie-consent-inner">
                        <div class="cookie-consent-text">
                            {!! \App\AppModel::getCookieConsentText() !!}
                        </div>

                        <div class="cookie-consent-button">
                            <button type="button" onclick="vue.clickedCookieConsentButton()">{{ __('app.cookie_consent_close') }}</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden is-margin-top-94">
                @guest
                    @if (env('APP_ABOUTPOSITION', 'bottom') === 'bottom')
                        @include('widgets.about', ['about_content' => \App\AppModel::getAboutContent()])
                    @endif
                @endguest
                @include('widgets.links')
                @include('widgets.bottom')
            </div>

            <div class="modal" :class="{'is-active': bShowRegister}">
                <div class="modal-background"></div>
                <div class="modal-card">
                    <header class="modal-card-head is-stretched">
                        <p class="modal-card-title">{{ __('app.register') }}</p>
                        <button class="delete" aria-label="close" onclick="window.vue.bShowRegister = false;"></button>
                        </header>
                        <section class="modal-card-body is-stretched">
                            <form id="regform" method="POST" action="{{ url('/register') }}">
                                @csrf

                                <div class="field">
                                    <label class="label">{{ __('app.register_name') }}</label>
                                    <div class="control">
                                        <input class="input" type="text" name="name" required>
                                    </div>
                                </div>

                                <div class="field">
                                    <label class="label">{{ __('app.register_email') }}</label>
                                    <div class="control">
                                        <input class="input" type="email" name="email" required>
                                    </div>
                                </div>

                                <div class="field">
                                    <label class="label">{{ __('app.register_password') }}</label>
                                    <div class="control">
                                        <input class="input" type="password" name="password" required>
                                    </div>
                                </div>

                                <div class="field">
                                    <label class="label">{{ __('app.register_password_confirmation') }}</label>
                                    <div class="control">
                                        <input class="input" type="password" name="password_confirmation" required>
                                    </div>
                                </div>

                                <div class="field">
                                    <label class="label">Captcha: {{ $captchadata[0] }} + {{ $captchadata[1] }} = ?</label>
                                    <div class="control">
                                        <input class="input" type="text" name="captcha" required>
                                    </div>
                                </div>

                                <div class="field">
                                    {!! \App\AppModel::getRegInfo()  !!}
                                </div>
                            </form>
                        </section>
                        <footer class="modal-card-foot is-stretched">
                        <span>
                            <button class="button is-success" onclick="document.getElementById('regform').submit();">{{ __('app.register') }}</button>
                        </span>
                        </footer>
                </div>
            </div>

            <div class="modal" :class="{'is-active': bShowLogin}">
                <div class="modal-background"></div>
                <div class="modal-card">
                    <header class="modal-card-head is-stretched">
                        <p class="modal-card-title">{{ __('app.login') }}</p>
                        <button class="delete" aria-label="close" onclick="window.vue.bShowLogin = false;"></button>
                        </header>
                        <section class="modal-card-body is-stretched">
                            <div>
                                <form id="loginform" method="POST" action="{{ url('/login') }}">
                                    @csrf

                                    <div class="field">
                                        <label class="label">{{ __('app.email') }}</label>
                                        <p class="control has-icons-left has-icons-right">
                                            <input class="input" onkeyup="window.vue.invalidLoginEmail()" onchange="window.vue.invalidLoginEmail()" onkeydown="if (event.keyCode === 13) { document.getElementById('loginform').submit(); }" type="email" name="email" id="loginemail" placeholder="{{ __('app.enteremail') }}" required>
                                            <span class="icon is-small is-left">
                                            <i class="fas fa-envelope"></i>
                                            </span>
                                        </p>
                                    </div>

                                    <div class="field">
                                        <label class="label">{{ __('app.password') }}</label>
                                        <p class="control has-icons-left">
                                            <input class="input" onkeyup="window.vue.invalidLoginPassword()" onchange="window.vue.invalidLoginPassword()" onkeydown="if (event.keyCode === 13) { document.getElementById('loginform').submit(); }" type="password" name="password" id="loginpw" placeholder="{{ __('app.enterpassword') }}" required>
                                            <span class="icon is-small is-left">
                                            <i class="fas fa-lock"></i>
                                            </span>
                                        </p>
                                    </div>
                                </form>
                            </div>

                            <div>
                                <br/>
                                <a href="javascript:void(0);" onclick="window.vue.bShowRegister = true; window.vue.bShowLogin = false;">{{ __('app.no_account_yet') }}</a>
                            </div>
                        </section>
                        <footer class="modal-card-foot is-stretched">
                        <span>
                            <button class="button is-success" onclick="document.getElementById('loginform').submit();">{{ __('app.login') }}</button>&nbsp;&nbsp;
                        </span>
                        <span class="is-right">
                            <div class="recover-pw">
                                <center><a href="javascript:void(0)" onclick="window.vue.bShowRecover = true; window.vue.bShowLogin = false;">{{ __('app.recover_password') }}</a></center>
                            </div>
                        </span>
                        </footer>
                </div>
            </div>

            <div class="modal" :class="{'is-active': bShowRecover}">
                <div class="modal-background"></div>
                <div class="modal-card">
                    <header class="modal-card-head is-stretched">
                    <p class="modal-card-title">{{ __('app.recover_password') }}</p>
                    <button class="delete" aria-label="close" onclick="window.vue.bShowRecover = false;"></button>
                    </header>
                    <section class="modal-card-body is-stretched">
                        <form method="POST" action="/recover" id="formResetPw">
                            @csrf

                            <div class="field">
                                <label class="label">{{ __('app.email') }}</label>
                                <div class="control">
                                    <input type="email" onkeyup="invalidRecoverEmail()" onchange="invalidRecoverEmail()" onkeydown="if (event.keyCode === 13) { document.getElementById('formResetPw').submit(); }" class="input" name="email" id="recoveremail" required>
                                </div>
                            </div>

                            <input type="submit" id="recoverpwsubmit" class="is-hidden">
                        </form>
                    </section>
                    <footer class="modal-card-foot is-stretched">
                    <button class="button is-success" onclick="document.getElementById('recoverpwsubmit').click();">{{ __('app.recover_password') }}</button>
                    <button class="button" onclick="window.vue.bShowRecover = false;">{{ __('app.cancel') }}</button>
                    </footer>
                </div>
            </div>

            <div class="modal" :class="{'is-active': bShowLinkFilter}">
                <div class="modal-background"></div>
                <div class="modal-card">
                    <header class="modal-card-head is-stretched">
                        <p class="modal-card-title" id="linkfilter-title"></p>
                        <button class="delete" aria-label="close" onclick="vue.bShowLinkFilter = false;"></button>
                    </header>
                    <section class="modal-card-body is-stretched">
                        <input type="hidden" id="linkfilter-url">

                        <p id="linkfilter-hint"></p>
                    </section>
                    <footer class="modal-card-foot is-stretched">
                        <button class="button is-success" onclick="window.open(document.getElementById('linkfilter-url').value); vue.bShowLinkFilter = false;">{{ __('app.linkfilter_visit') }}</button>
                        <button class="button" onclick="vue.bShowLinkFilter = false;">{{ __('app.cancel') }}</button>
                    </footer>
                </div>
            </div>

            @auth
            <div class="modal" :class="{'is-active': bShowCreateActivity}">
                <div class="modal-background"></div>
                <div class="modal-card">
                    <header class="modal-card-head is-stretched">
                        <p class="modal-card-title">{{ __('app.create_activity') }}</p>
                        <button class="delete" aria-label="close" onclick="vue.bShowCreateActivity = false;"></button>
                    </header>
                    <section class="modal-card-body is-stretched">
                        <form id="frmCreateActivity" method="POST" action="{{ url('/activity/create') }}">
                            @csrf

                            <div class="field">
                                <label class="label">{{ __('app.title') }}</label>
                                <div class="control">
                                    <input id="caTitle" class="input" type="text" name="title" value="{{ old('title') }}" onkeyup="window.vue.invalidCreateActivity();" onchange="window.vue.invalidCreateActivity();" required>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.description') }}</label>
                                <div class="control">
                                    <textarea id="caDescription" name="description" onkeyup="window.vue.invalidCreateActivity();" onchange="window.vue.invalidCreateActivity();" required>{{ old('description') }}</textarea>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.date_from') }}</label>
                                <div class="control">
                                    <input id="caDateFrom" class="input" type="date" name="date_of_activity_from" onkeyup="window.vue.invalidCreateActivity();" onchange="window.vue.invalidCreateActivity(); document.getElementById('caDateTill').value = this.value;" required>
                                </div>
                                <p class="help is-danger is-hidden" id="activity-date-hint">{{ __('app.date_is_in_past') }}</p>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.date_till') }}</label>
                                <div class="control">
                                    <input id="caDateTill" class="input" type="date" name="date_of_activity_till" onkeyup="window.vue.invalidCreateActivity();" onchange="window.vue.invalidCreateActivity();" required>
                                </div>
                                <p class="help is-danger is-hidden" id="activity-date-hint">{{ __('app.date_is_in_past') }}</p>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.time') }}</label>
                                <div class="control">
                                    <input id="caTime" class="input" type="time" name="time_of_activity" value="{{ old('time_of_activity') }}" onkeyup="window.vue.invalidCreateActivity();" onchange="window.vue.invalidCreateActivity();" required>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.category') }}</label>
                                <div class="control">
                                    <select name="category">
                                        @foreach (\App\CategoryModel::fetch() as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.location') }}</label>
                                <div class="control">
                                    <input type="text" class="input" name="location" id="caLocation" placeholder="{{ __('app.location') }}" value="{{ old('location') }}" onkeyup="document.getElementById('location-list-content-create').innerHTML = ''; window.vue.queryLocation(this, 'create', 'caLocation');" required>
                                    <div class="dropdown is-left is-inline-block" id="location-list-create">
                                        <div class="dropdown-menu is-color-black-force" role="menu">
                                            <div class="dropdown-content" id="location-list-content-create">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="field fix-checkbox-top">
                                <div class="control">
                                    <input type="checkbox" data-role="checkbox" data-type="2" data-caption="{{ __('app.add_as_participant') }}" name="add_participant" value="1" checked>
                                </div>
                            </div>

                            <div class="field">
                                <div class="control">
                                    <a href="javascript:void(0);" onclick="document.getElementById('additional-options').classList.toggle('is-hidden'); this.remove();">{{ __('app.additional_options') }}</a>
                                </div>
                            </div>

                            <div id="additional-options" class="is-hidden">
                                <div class="field">
                                    <label class="label">{{ __('app.limit') }}</label>
                                    <div class="control">
                                        <input class="input" type="number" name="limit" value="0" min="0">
                                    </div>
                                </div>

                                <div class="field">
                                    <label class="label">{{ __('app.only_gender') }}</label>
                                    <div class="control">
                                        <select name="gender">
                                            <option value="0">{{ __('app.all') }}</option>
                                            <option value="1">{{ __('app.gender_male') }}</option>
                                            <option value="2">{{ __('app.gender_female') }}</option>
                                            <option value="3">{{ __('app.gender_diverse') }}</option>
                                        </select>
                                    </div>
                                </div>

                                @if (\App\VerifyModel::getState(auth()->id()) == \App\VerifyModel::STATE_VERIFIED)
                                <div class="field">
                                    <label class="label">{{ __('app.only_verified') }}</label>
                                    <div class="control">
                                        <input type="checkbox" data-role="checkbox" data-type="2" data-caption="{{ __('app.only_verified_long') }}" name="only_verified" value="1">
                                    </div>
                                </div>
                                @endif
                            </div>
                        </form>
                    </section>
                    <footer class="modal-card-foot is-stretched">
                        <span>
                            <button id="btnCreateActivity" class="button is-success" onclick="if (!this.disabled) { document.getElementById('frmCreateActivity').submit(); }">{{ __('app.create') }}</button>
                        </span>
                    </footer>
                </div>
            </div>

                <div class="modal" :class="{'is-active': bShowReplyThread}">
                    <div class="modal-background"></div>
                    <div class="modal-card">
                        <header class="modal-card-head is-stretched">
                            <p class="modal-card-title">{{ __('app.reply_thread') }}</p>
                            <button class="delete" aria-label="close" onclick="vue.bShowReplyThread = false;"></button>
                        </header>
                        <section class="modal-card-body is-stretched">
                            <form id="formReplyThread">
                                @csrf

                                <input type="hidden" id="thread-reply-parent" name="parent">

                                <div class="field">
                                    <label class="label">{{ __('app.text') }}</label>
                                    <div class="control">
                                        <textarea name="text" id="thread-reply-textarea"></textarea>
                                    </div>
                                </div>

                                <input type="button" id="replythreadsubmit" onclick="window.vue.replyThread(document.getElementById('thread-reply-parent').value, document.getElementById('thread-reply-textarea').value); vue.bShowReplyThread = false;" class="is-hidden">
                            </form>
                        </section>
                        <footer class="modal-card-foot is-stretched">
                            <button class="button is-success" onclick="document.getElementById('replythreadsubmit').click();">{{ __('app.submit') }}</button>
                            <button class="button" onclick="vue.bShowReplyThread = false;">{{ __('app.cancel') }}</button>
                        </footer>
                    </div>
                </div>

                <div class="modal" :class="{'is-active': bShowEditComment}">
                    <div class="modal-background"></div>
                    <div class="modal-card">
                        <header class="modal-card-head is-stretched">
                            <p class="modal-card-title">{{ __('app.edit_comment') }}</p>
                            <button class="delete" aria-label="close" onclick="vue.bShowEditComment = false;"></button>
                        </header>
                        <section class="modal-card-body is-stretched">
                            <input type="hidden" id="editCommentId">

                            <form id="frmEditComment" method="POST">
                                @csrf

                                <div class="field">
                                    <label class="label">{{ __('app.text') }}</label>
                                    <div class="control">
                                        <textarea name="text" id="editCommentText"></textarea>
                                    </div>
                                </div>
                            </form>
                        </section>
                        <footer class="modal-card-foot is-stretched">
                            <button class="button is-success" onclick="document.getElementById('frmEditComment').submit();">{{ __('app.save') }}</button>
                            <button class="button" onclick="vue.bShowEditComment = false;">{{ __('app.cancel') }}</button>
                        </footer>
                    </div>
                </div>

                @yield('modal')
            @endauth
        </div>

        @if (env('APP_ENABLESCROLLER'))
        <div class="scroll-to-top">
            <div class="scroll-to-top-inner">
                <a href="javascript:void(0);" onclick="document.querySelector('.navbar').scrollIntoView({ behavior: 'smooth' });"><i class="fas fa-arrow-up fa-2x up-color"></i></a>
            </div>
        </div>
        @endif

        <script src="{{ asset('js/app.js') }}"></script>
    </body>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.menuVisible = false;

            window.vue.app_project = '{{ env('APP_PROJECTNAME') }}';
            window.vue.lang.copiedToClipboard = '{{ __('app.copiedToClipboard') }}';
            window.vue.lang.edit = '{{ __('app.edit') }}';
            window.vue.lang.lock = '{{ __('app.lock') }}';
            window.vue.lang.expandThread = '{{ __('app.expandThread') }}';
            window.vue.lang.reply = '{{ __('app.reply') }}';
            window.vue.lang.report = '{{ __('app.report') }}';
            window.vue.lang.ignore = '{{ __('app.add_to_ignore') }}';
            window.vue.lang.view = '{{ __('app.view') }}';
            window.vue.lang.remove = '{{ __('app.remove') }}';
            window.vue.lang.verifiedUser = '{{ __('app.verifiedUser') }}';
            window.vue.lang.confirmLockForumPost = '{{ __('app.confirmLockForumPost') }}';
            window.vue.lang.forumPostEdited = '{{ __('app.forum_post_edited_info') }}';
            window.vue.lang.share_whatsapp = '{{ __('app.share_whatsapp') }}';
            window.vue.lang.share_twitter = '{{ __('app.share_twitter') }}';
            window.vue.lang.share_facebook = '{{ __('app.share_facebook') }}';
            window.vue.lang.share_sms = '{{ __('app.share_sms') }}';
            window.vue.lang.share_email = '{{ __('app.share_email') }}';
            window.vue.lang.share_clipboard = '{{ __('app.share_clipboard') }}';
            window.vue.lang.marketplace_advert_by = '{{ __('app.marketplace_advert_by') }}';
            window.vue.lang.linkfilter_title = '{{ __('app.linkfilter_title') }}';
            window.vue.lang.linkfilter_hint = '{{ __('app.linkfilter_hint') }}';
            window.vue.lang.gallery_item_by = '{{ __('app.gallery_item_by') }}';
            window.vue.lang.noTagsSpecified = '{{ __('app.no_tags_specified') }}';
            window.vue.lang.imageSent = '{{ __('app.image_sent') }}';
            window.vue.lang.confirmLocGalleryThreadItem = '{{ __('app.confirmLocGalleryThreadItem') }}';

            window.vue.numeric_vars.activity_max_username_length = {{ env('APP_ACTIVITY_MAX_USERNAME_LENGTH', 35) }};
            window.vue.numeric_vars.activity_max_title_length = {{ env('APP_ACTIVITY_MAX_TITLE_LENGTH', 40) }};
            window.vue.numeric_vars.activity_max_description_length = {{ env('APP_ACTIVITY_MAX_DESCRIPTION_LENGTH', 90) }};

            @auth
                window.user = {};
                window.user.id = {{ auth()->id() }};
                window.user.data = JSON.parse(`{!! \App\User::getAuthExposableData() !!}`);
            @elseguest
                window.user = null;
            @endauth

            window.vue.handleCookieConsent();

            @if (Session::has('flash.error'))
                setTimeout('window.vue.showError()', 500);
            @endif

            @if (Session::has('flash.success'))
                setTimeout('window.vue.showSuccess()', 500);
            @endif

            @auth
                setTimeout('fetchNotifications()', 1000);
                setTimeout('fetchNotificationList()', 100);
                setTimeout('fetchFavorites()', 1000);
                setTimeout('fetchFavActivities()', 1000);
            @endauth

            const $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);

            if ($navbarBurgers.length > 0) {
                $navbarBurgers.forEach(el => {
                    el.addEventListener('click', () => {
                        const target = el.dataset.target;
                        const $target = document.getElementById(target);

                        el.classList.toggle('is-active');
                        $target.classList.toggle('is-active');
                    });
                });
            }
        });

        window.fetchNotifications = function() {
            window.vue.ajaxRequest('get', '{{ url('/notifications/list?mark=0') }}', {}, function(response){
                if (response.code === 200) {
                    if (response.data.length > 0) {
                        let noyet = document.getElementById('no-notifications-yet');
                        if (noyet) {
                            noyet.remove();
                        }

                        let indicator = document.getElementById('navbar-notify-wrapper');
                        if (indicator) {
                            indicator.classList.remove('is-hidden');

                            count = document.getElementById('navbar-notify-count');
                            if (count) {
                                count.innerHTML = response.data.length;
                            }
                        }

                        let burgerSpan = document.getElementById('burger-notification');
                        if (burgerSpan) {
                            burgerSpan.style.display = 'unset';
                        }

                        response.data.forEach(function(elem, index) {
                            if (document.getElementById('notification-item-' + elem.id) === null) {
                                @if (isset($_GET['clep_push_handler']))
                                    window['{{ $_GET['clep_push_handler'] }}'](elem.shortMsg, elem.longMsg);
                                @endif

                                let html = window.vue.renderNotification(elem, true);
                                document.getElementById('notification-content').innerHTML = html + document.getElementById('notification-content').innerHTML;
                            }
                        });
                    }
                }
            });

            setTimeout('fetchNotifications()', 50000);
        };

        window.notificationPagination = null;
        window.fetchNotificationList = function() {
            document.getElementById('notification-content').innerHTML += '<center><i id="notification-spinner" class="fas fa-spinner fa-spin"></i></center>';

            let loader = document.getElementById('load-more-notifications');
            if (loader) {
                loader.remove();
            }

            window.vue.ajaxRequest('get', '{{ url('/notifications/fetch') }}' + ((window.notificationPagination) ? '?paginate=' + window.notificationPagination : ''), {}, function(response) {
                if (response.code === 200) {
                    if (response.data.length > 0) {
                        let noyet = document.getElementById('no-notifications-yet');
                        if (noyet) {
                            noyet.remove();
                        }

                        response.data.forEach(function(elem, index) {
                            let html = window.vue.renderNotification(elem);

                            document.getElementById('notification-content').innerHTML += html;
                        });

                        window.notificationPagination = response.data[response.data.length-1].id;

                        document.getElementById('notification-content').innerHTML += '<center><i id="load-more-notifications" class="fas fa-plus is-pointer" onclick="fetchNotificationList()"></i></center>';
                        document.getElementById('notification-spinner').remove();
                    } else {
                        if (window.notificationPagination === null) {
                            document.getElementById('notification-content').innerHTML = '<div id="no-notifications-yet"><center><i>{{ __('app.no_notifications_yet') }}</i></center></div>';
                        }

                        let loader = document.getElementById('load-more-notifications');
                        if (loader) {
                            loader.remove();
                        }

                        let spinner = document.getElementById('notification-spinner');
                        if (spinner) {
                            spinner.remove();
                        }
                    }
                }
            });
        };

        window.fetchFavorites = function() {
            window.vue.ajaxRequest('get', '{{ url('/favorites/fetch') }}', {}, function(response) {
                document.getElementById('favorites-content').innerHTML = '';
                if (response.code === 200) {
                    if (response.data.length > 0) {
                        response.data.forEach(function(elem, index) {
                            let html = window.vue.renderFavorite(elem);

                            document.getElementById('favorites-content').innerHTML += html;

                            if (elem.activityCount > 0) {
                                document.getElementById('favorite-activity-count-' + elem.entityId).innerHTML = elem.activityCount;
                                document.getElementById('favorite-activity-count-' + elem.entityId).classList.remove('is-hidden');
                            }
                        });
                    } else {
                        document.getElementById('favorites-content').innerHTML = '{{ __('app.no_favorites_yet') }}';
                    }
                } else {
                    console.log(response.msg);
                }
            });
        };

        @auth
            window.fetchFavActivities = function() {
                window.vue.ajaxRequest('get', '{{ url('/activity/user/' . auth()->id() . '/participations') }}', {}, function(response){
                    if (response.code === 200) {
                        if (response.data.length > 0) {
                            document.getElementById('favorites-act-content').innerHTML = '';
                            
                            response.data.forEach(function(elem, index) {
                                elem.user = JSON.parse('<?= json_encode(\App\User::getByAuthId()->toArray()) ?>');
                                let html = window.vue.renderActivitySmall(elem);

                                document.getElementById('favorites-act-content').innerHTML += html;
                            });
                        } else {
                            document.getElementById('favorites-act-content').innerHTML = '{{ __('app.no_favorites_yet') }}';
                        }
                    }
                });
            };
        @endauth
    </script>

    @yield('javascript')
</html>

{{--
    ComAct (dnyComAct) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', App::getLocale()) }}">
    <head>
        @if (env('GA_TOKEN'))
            <!-- Global site tag (gtag.js) - Google Analytics -->
            <script async src="https://www.googletagmanager.com/gtag/js?id={{ env('GA_TOKEN') }}"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', '{{ env('GA_TOKEN') }}');
            </script>
        @endif

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ env('APP_NAME') }} - {{ env('APP_DESCRIPTION') }}</title>

        <meta name="author" content="{{ env('APP_AUTHOR') }}">
        <meta name="description" content="{{ env('APP_METADESC') }}">
        <meta name="tags" content="{{ env('APP_METATAGS') }}">

        <link rel="shortcut icon" href="{{ asset('gfx/logo.png') }}">

        <link rel="stylesheet" type="text/css" href="{{ asset('css/bulma.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/metro-all.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">

        @if (env('APP_ENV') == 'local')
        <script src="{{ asset('js/vue.js') }}"></script>
        @elseif (env('APP_ENV') == 'production')
        <script src="{{ asset('js/vue.min.js') }}"></script>
        @endif
        <script src="{{ asset('js/fontawesome.js') }}"></script>
        <script src="{{ asset('js/metro.min.js') }}"></script>
    </head>

    <body>
        <div id="app">
            <nav class="navbar is-info" role="navigation" aria-label="main navigation">
                <div class="navbar-brand">
                    <a class="navbar-item" href="{{ url('/') }}">
                        <strong>{{ env('APP_PROJECTNAME') }}</strong>
                    </a>
                </div>

                <a role="button" class="navbar-burger burger" aria-label="menu" aria-expanded="false" data-target="navMainMenu">
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                </a>

                <div id="navMainMenu" class="navbar-menu">
                <div class="navbar-start">
                </div>

                <div class="navbar-end">
                    @auth
                        <a class="button navbar-item is-outlined is-success navbar-fixed-button-top" href="javascript:void(0);" onclick="window.vue.bShowCreateActivity = true; document.getElementById('btnCreateActivity').disabled = true;">
                            {{ __('app.create_activity') }}
                        </a>

                        <a class="navbar-item" href="{{ url('/notifications') }}">
                            {{ __('app.notifications') }}
                        </a>

                        <a class="navbar-item" href="{{ url('/messages') }}">
                            {{ __('app.messages') }}
                        </a>

                        <a class="navbar-item" href="{{ url('/settings') }}">
                            {{ __('app.settings') }}
                        </a>

                        <a class="navbar-item" href="{{ url('/logut') }}">
                            {{ __('app.logout') }}
                        </a>
                    @endauth

                    @guest
                    <div class="navbar-item">
                    <div class="buttons">
                        <a class="button is-light is-bold is-outlined" href="javascript:void(0);" onclick="vue.bShowRegister = true;">
                            {{ __('app.register') }}
                        </a>
                        &nbsp;&nbsp;
                        <a class="navbar-login" href="javascript:void(0);" onclick="vue.bShowLogin = true;">
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
                            {{ Session::get('success') }}
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

            @include('widgets.banner')

            <div class="container">
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

            <div class="overflow-hidden">
                @guest
                    @include('widgets.howto')
                    @include('widgets.about')
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
                        </section>
                        <footer class="modal-card-foot is-stretched">
                        <span>
                            <button class="button is-success" onclick="document.getElementById('loginform').submit();">{{ __('app.login') }}</button>
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
                                    <input id="caTitle" class="input" type="text" name="title" onkeyup="window.vue.invalidCreateActivity();" onchange="window.vue.invalidCreateActivity();" required>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.description') }}</label>
                                <div class="control">
                                    <textarea id="caDescription" name="description" onkeyup="window.vue.invalidCreateActivity();" onchange="window.vue.invalidCreateActivity();" required></textarea>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.date') }}</label>
                                <div class="control">
                                    <input id="caDate" class="input" type="date" name="date_of_activity" onkeyup="window.vue.invalidCreateActivity();" onchange="window.vue.invalidCreateActivity();" required>
                                </div>
                                <p class="help is-danger is-hidden" id="activity-date-hint">{{ __('app.date_is_in_past') }}</p>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.location') }}</label>
                                <div class="control">
                                    <input id="caLocation" class="input" type="text" name="location" onkeyup="window.vue.invalidCreateActivity();" onchange="window.vue.invalidCreateActivity();" required>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.limit') }}</label>
                                <div class="control">
                                    <input class="input" type="number" name="limit" value="0" min="0">
                                </div>
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
            @endauth
        </div>

        <script src="{{ asset('js/app.js') }}"></script>
    </body>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.vue.handleCookieConsent();

            @if (Session::has('flash.error'))
                setTimeout('window.vue.showError()', 500);
            @endif

            @if (Session::has('flash.success'))
                setTimeout('window.vue.showSuccess()', 500);
            @endif

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
    </script>

    @yield('javascript')
</html>

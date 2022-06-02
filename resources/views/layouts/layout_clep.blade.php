{{--
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2022 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', App::getLocale()) }}" class="clep-outer">
    @include('layouts.layout_head') 

    <body class="clep-outer" @if (file_exists(public_path() . '/gfx/backgrounds/' . \App\AppModel::getClepBackground())) style="background-image: url('{{ asset('gfx/backgrounds/' . \App\AppModel::getClepBackground()) }}');" @endif>
        <div id="app" @if (file_exists(public_path() . '/gfx/backgrounds/clep.png')) style="background-color: rgba(0, 0, 0, {{ \App\Models\AppModel::getAlphaChannel() }});" @endif>
            <div class="clep-content">
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
                                {!! Session::get('error') !!}
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

                @yield('content')
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

            <div class="navbar is-fixed-bottom">
                @include("widgets.links")
            </div>
        </div>
    </body>
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            let el = document.getElementsByClassName('linklist')[0];

            if (window.innerWidth <= 768) {
                el.style.top = '-16px';
            } else if ((window.innerWidth > 768) && (window.innerWidth <= 1087)) {
                el.style.top = '5px';
            } else {
                el.style.top = '12px';
            }
        })
    </script>
</html>

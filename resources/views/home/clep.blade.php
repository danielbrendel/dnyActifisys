{{--
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2023 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

@extends('layouts.layout_clep')

@section('content')
    <div>
        <h1 class="clep-headline @if (file_exists(public_path() . '/gfx/backgrounds/' . \App\AppModel::getClepBackground())) {{ 'is-color-white' }} @endif">
            <center>
                <strong>{{ env('APP_NAME') }}</strong>
            </center>
        </h1>

        <div class="clep-notice-content @if (file_exists(public_path() . '/gfx/backgrounds/' . \App\AppModel::getClepBackground())) {{ 'is-color-white' }} @endif">
            <center>{{ env('APP_DESCRIPTION') }}</center>
        </div>

        <form id="loginform" method="POST" action="{{ url('/login') }}">
            @csrf

            <input type="hidden" name="device_token" id="device_token" value="">

            <div class="field">
                <label class="label @if (file_exists(public_path() . '/gfx/backgrounds/' . \App\AppModel::getClepBackground())) {{ 'is-color-white' }} @endif">{{ __('app.email') }}</label>
                <p class="control has-icons-left has-icons-right">
                    <input class="input @if (file_exists(public_path() . '/gfx/backgrounds/' . \App\AppModel::getClepBackground())) {{ 'is-background-white-transparent' }} @endif" onkeyup="window.vue.invalidLoginEmail()" onchange="window.vue.invalidLoginEmail()" onkeydown="if (event.keyCode === 13) { document.getElementById('loginform').submit(); }" type="email" name="email" id="loginemail" placeholder="{{ __('app.email') }}" required>
                    <span class="icon is-small is-left">
                        <i class="fas fa-envelope"></i>
                    </span>
                </p>
            </div>

            <div class="field">
                <label class="label @if (file_exists(public_path() . '/gfx/backgrounds/' . \App\AppModel::getClepBackground())) {{ 'is-color-white' }} @endif">{{ __('app.password') }}</label>
                <p class="control has-icons-left">
                    <input class="input @if (file_exists(public_path() . '/gfx/backgrounds/' . \App\AppModel::getClepBackground())) {{ 'is-background-white-transparent' }} @endif" onkeyup="window.vue.invalidLoginPassword()" onchange="window.vue.invalidLoginPassword()" onkeydown="if (event.keyCode === 13) { document.getElementById('loginform').submit(); }" type="password" name="password" id="loginpw" placeholder="{{ __('app.password') }}" required>
                    <span class="icon is-small is-left">
                        <i class="fas fa-lock"></i>
                    </span>
                </p>
            </div>

            <div>
                <div class="is-inline-block">
                    <button class="button is-success" onclick="window.vue.setClepFlag(); document.getElementById('loginform').submit();">{{ __('app.login') }}</button>
                </div>

                <div class="is-inline-block float-right clep-recover-top recover-pw">
                    <a @if (file_exists(public_path() . '/gfx/backgrounds/' . \App\AppModel::getClepBackground())) class="is-color-blue" @endif href="javascript:void(0)" onclick="window.vue.bShowRecover = true;">{{ __('app.recover_password') }}</a>
                </div>
            </div>

            <div class="clep-border clep-signup">
                <center><a @if (file_exists(public_path() . '/gfx/backgrounds/' . \App\AppModel::getClepBackground())) class="is-color-blue" @endif href="javascript:void(0)" onclick="window.vue.bShowRegister = true;">{{ __('app.register') }}</a></center>
            </div>
        </form>
    </div>
@endsection

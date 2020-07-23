{{--
    Danigram (dnyDanigram) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

@extends('layouts.layout_email')

@section('title')
    {{ __('app.mail_password_reset_title') }}
@endsection

@section('body')
    {{ __('app.mail_salutation', ['name' => $username]) }}
    <br/><br/>
    {{ __('app.mail_password_reset_body') }}
@endsection

@section('action')
    <a class="button" href="{{ url('/reset?hash=' . $hash) }}">{{ __('app.mail_password_reset') }}</a>
@endsection

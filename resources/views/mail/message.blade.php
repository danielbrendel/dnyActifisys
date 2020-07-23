{{--
    Danigram (dnyDanigram) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

@extends('layouts.layout_mail')

@section('title')
    {{ __('app.mail_message_received_title') }}
@endsection

@section('body')
    <strong><i>{{ __('app.mail_message_received_info') }}</i></strong>
    <br/><br/>
    {{ __('app.mail_salutation', ['name' => $name]) }}
    <br/><br/>
    {{ __('app.mail_message_received_body') }}
    <br/><br/>
    <hr/>
    <strong>{{ $sender }}:</strong><br/>
    <pre>{{ $message }}</pre>
    <hr/>
    <br/>
@endsection

@section('action')
    <a class="button" href="{{ url('/messages/show/' . $msgid) }}" target="_blank">{{ __('app.mail_message_open') }}</a>
@endsection

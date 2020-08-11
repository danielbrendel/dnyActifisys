{{--
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

@extends('layouts.layout_email')

@section('title')
    {{ __('app.mail_activity_canceled_title') }}
@endsection

@section('body')
    <strong><i>{{ __('app.mail_activity_canceled_info') }}</i></strong>
    <br/><br/>
    {{ __('app.mail_salutation', ['name' => $name]) }}
    <br/><br/>
    {{ __('app.mail_activity_canceled_body', ['title' => $activity->title, 'name' => $owner->name]) }}
    <br/><br/>
    <pre>{{ ((strlen($activity->cancelReason) > 0) ? $activity->cancelReason : __('app.no_reason_specified')) }}</pre>
    <br/>
@endsection

@section('action')
    <a class="button" href="{{ url('/activity/' . $activity->id) }}" target="_blank">{{ __('app.mail_activity_open') }}</a>
@endsection

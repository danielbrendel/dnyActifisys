{{--
    ComAct (dnyComAct) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

@extends('layouts.layout_email')

@section('title')
    {{ __('app.mail_user_participated_title') }}
@endsection

@section('body')
    <strong><i>{{ __('app.mail_user_participated_info') }}</i></strong>
    <br/><br/>
    {{ __('app.mail_salutation', ['name' => $name]) }}
    <br/><br/>
    {{ __('app.mail_user_participated_body', ['name' => $participant->name, 'id' => $participant->id]) }}
    <br/><br/>
	<strong>{{ $activity->title }}:</strong><br/>
    <pre>{{ $activity->description }}</pre>
	<br/>
@endsection

@section('action')
    <a class="button" href="{{ url('/activity/' . $activity->id) }}" target="_blank">{{ __('app.mail_activity_open') }}</a>
@endsection

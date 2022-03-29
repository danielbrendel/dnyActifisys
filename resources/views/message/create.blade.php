{{--
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2022 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

@extends('layouts.layout_home')

@section('title')
    {{ __('app.message_create') }}
@endsection

@section('content')
    <div class="column is-2"></div>

    <div class="column is-8 fixed-form">
        <h1>{{ __('app.message_create') }}</h1>

        <div class="member-form is-default-padding member-form-fixed-top">
            <form method="POST" action="{{ url('/messages/send') }}">
                @csrf

                <input type="hidden" name="user" value=" {{ $_GET['userId'] }}">

                <div class="field">
                    <label class="label">{{ __('app.subject') }}</label>
                    <div class="control">
                        <input type="text" name="subject" placeholder="{{ __('app.subject') }}" value="{{ old('subject') }}">
                    </div>
                </div>

                <div class="field">
                    <label class="label">{{ __('app.text') }}</label>
                    <div class="control">
                        <textarea name="text" placeholder="{{ __('app.type_something') }}">{{ old('text') }}</textarea>
                    </div>
                </div>

                <div class="field">
                    <input type="submit" value="{{ __('app.send') }}">
                </div>
            </form>
        </div>
    </div>

    <div class="column is-2 is-sidespacing"></div>
@endsection


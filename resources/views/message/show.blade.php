{{--
    Danigram (dnyDanigram) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

@extends('layouts.layout_home')

@section('content')
    <div class="column is-2"></div>

    <div class="column is-8">
        <h1>{{ __('app.message_thread', ['name' => $thread['msg']->sender->name]) }}</h1>

        <div class="is-default-padding">
            <form method="POST" action="{{ url('/messages/send') }}">
                @csrf

                <input type="hidden" name="user" value="{{ $thread['msg']->userId }}">

                <div class="field">
                    <label class="label">{{ __('app.subject') }}</label>
                    <div class="control">
                        <input type="text" name="subject" value="{{ $thread['msg']->subject }}">
                    </div>
                </div>

                <div class="field">
                    <label class="label">{{ __('app.text') }}</label>
                    <div class="control">
                        <textarea name="text" placeholder="{{ __('app.type_something') }}"></textarea>
                    </div>
                </div>

                <div class="field">
                    <input type="submit" value="{{ __('app.send') }}">
                </div>
            </form>
        </div>

        <div class="member-form is-default-padding">
            <div class="message-thread">
                <div class="message-thread-header">
                    <div class="message-thread-header-avatar">
                        <img src="{{ asset('gfx/avatars/' . $thread['msg']->sender->avatar) }}">
                    </div>

                    <div class="message-thread-header-userinfo">
                        <div>{{ $thread['msg']->sender->name }}</div>
                        <div title="{{ $thread['msg']->created_at }}">{{ $thread['msg']->created_at->diffForHumans() }}</div>
                    </div>

                    <div class="message-thread-header-subject">{{ $thread['msg']->subject }}</div>
                </div>

                <div class="message-thread-text">{{ $thread['msg']->message }}</div>
            </div>

            @foreach ($thread['previous'] as $msg)
                <div class="message-thread">
                    <div class="message-thread-header">
                        <div class="message-thread-header-avatar">
                            <img src="{{ asset('gfx/avatars/' . $msg->sender->avatar) }}">
                        </div>

                        <div class="message-thread-header-userinfo">
                            <div>{{ $msg->sender->name }}</div>
                            <div title="{{ $msg->created_at }}">{{ $msg->created_at->diffForHumans() }}</div>
                        </div>

                        <div class="message-thread-header-subject">{{ $msg->subject }}</div>
                    </div>

                    <div class="message-thread-text">{{ $msg->message }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="column is-2 is-sidespacing"></div>
@endsection


{{--
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2023 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

@extends('layouts.layout_home')

@section('title')
    {{ __('app.message_thread', ['name' => $msg->message_partner->name]) }}
@endsection

@section('content')
    <div class="column is-2"></div>

    <div class="column is-8 fixed-form">
        <h1>{{ __('app.message_thread', ['name' => $msg->message_partner->name]) }}</h1>

        <div class="is-default-padding">
            <form method="POST" action="{{ url('/messages/send') }}" id="frmSendMessage" class="has-transparent-input">
                @csrf

                <input type="hidden" name="user" value="{{ $msg->message_partner->id }}">

                <div class="field">
                    <label class="label">{{ __('app.subject') }}</label>
                    <div class="control">
                        <input type="text" name="subject" value="{{ $msg->subject }}">
                    </div>
                </div>

                <div class="field">
                    <label class="label">{{ __('app.text') }}</label>
                    <div class="control">
                        <textarea name="text" placeholder="{{ __('app.type_something') }}"></textarea>
                    </div>
                </div>
            </form>

            <form method="POST" action="{{ url('/messages/image') }}" enctype="multipart/form-data" id="frmSendImage">
                @csrf

                <input type="hidden" name="user" value="{{ $msg->message_partner->id }}">
                <input type="hidden" name="subject" value="{{ $msg->subject }}">

                <input type="file" name="image" id="inpImage" class="is-hidden">
            </form>

            <div class="field is-margin-top-10">
                <span><a class="button is-link" href="javascript:void(0);" onclick="document.getElementById('frmSendMessage').submit();">{{ __('app.send') }}</a></span>
                <span>&nbsp;<a class="is-underline" href="javascript:void(0);" onclick="window.vue.sendImage('inpImage', 'frmSendImage');">{{ __('app.send_image') }}</a></span>
            </div>
        </div>

        <div class="member-form is-default-padding" id="message-thread-content"></div>
    </div>

    <div class="column is-2 is-sidespacing"></div>
@endsection

@section('javascript')
<script>
        window.paginate = null;

        window.queryMessages = function() {
            let content = document.getElementById('message-thread-content');

            content.innerHTML += '<div id="spinner"><center><i class="fa fa-spinner fa-spin"></i></center></div>';

            let loadmore = document.getElementById('loadmore');
            if (loadmore) {
                loadmore.remove();
            }

            window.vue.ajaxRequest('post', '{{ url('/messages/query') }}', {
                id: '{{ $msg->channel }}',
                paginate: window.paginate
            },
            function(response) {
                if (response.code == 200) {
                    response.data.forEach(function(elem, index) {
                        let html = window.vue.renderMessageItem(elem, {{ auth()->id() }});

                        content.innerHTML += html;
                    });

                    if (response.data.length > 0) {
                        window.paginate = response.data[response.data.length - 1].id;
                    }

                    let spinner = document.getElementById('spinner');
                    if (spinner) {
                        spinner.remove();
                    }

                    if (response.data.length > 0) {
                        content.innerHTML += '<div id="loadmore" class="is-pointer" onclick="window.queryMessages();"><center><br/>{{ __('app.load_more') }}</center></div>';
                    }
                } else {
                    console.error(response.msg);
                }
            });
        };

        document.addEventListener('DOMContentLoaded', function() {
            window.queryMessages();
        });
    </script>
@endsection

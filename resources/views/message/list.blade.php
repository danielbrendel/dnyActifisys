{{--
    ComAct (dnyComAct) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

@extends('layouts.layout_home')

@section('title')
    {{ __('app.messages') }}
@endsection

@section('content')
    <div class="column is-2"></div>

    <div class="column is-8 fixed-form">
        <div class="is-default-padding">
            <div>
                <h1>{{ __('app.messages') }}</h1>
            </div>

            <div class="messages">
                <div class="messages-list" id="messages-list">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
            </div>

            <div class="messages-footer">
                <div class="messages-footer-count" id="msg-count"></div>

                <div class="messages-footer-nav">
                    <span><i id="browse-left" class="fas fa-arrow-left is-pointer" onclick="if (window.paginateList < window.maxMsgId) window.paginateList = window.previousMsgId+1; window.msgListCounter -= {{ env('APP_MESSAGEPACKLIMIT') }}; fetchMessageList()"></i></span>
                    <span><i id="browse-right" class="fas fa-arrow-right is-pointer" onclick="if (window.paginateList > window.minMsgId) fetchMessageList(); window.msgListCounter += {{ env('APP_MESSAGEPACKLIMIT') }};"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="column is-2"></div>
@endsection

@section('javascript')
    <script>
        window.paginateList = null;
        window.previousMsgId = null;
        window.msgListCounter = 1;

        window.fetchMessageList = function() {
            document.getElementById('messages-list').innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            window.vue.ajaxRequest('get', '{{ url('/messages/list') }}' + ((window.paginateList !== null) ? '?paginate=' + window.paginateList : ''), {}, function(response) {
                if (response.code === 200) {
                 document.getElementById('messages-list').innerHTML = '';

                 if (response.max === null) {
                     document.getElementById('messages-list').innerHTML = '{{ __('app.no_messages') }}';
                     return;
                 }

                 response.data.forEach(function(elem, index){
                    let html = window.vue.renderMessageListItem(elem);

                    document.getElementById('messages-list').innerHTML += html;
                 });

                 window.minMsgId = response.min;
                 window.maxMsgId = response.max;

                 window.previousMsgId = window.paginateList;
                 if (response.data.length > 0) {
                     window.paginateList = response.data[response.data.length - 1].id;
                 }

                  document.getElementById('browse-left').classList.remove('is-color-grey');
                  document.getElementById('browse-right').classList.remove('is-color-grey');

                 if (window.paginateList <= response.min) {
                     document.getElementById('browse-right').classList.add('is-color-grey');
                 } else if (window.paginateList >= response.max) {
                     document.getElementById('browse-left').classList.add('is-color-grey');
                 }

                 document.getElementById('msg-count').innerHTML = window.msgListCounter + ' to ' + (window.msgListCounter + {{ env('APP_MESSAGEPACKLIMIT') }}) + ' of total ' + response.max;
             }
          });
        };

        document.addEventListener('DOMContentLoaded', function() {
           window.fetchMessageList();
        });
    </script>
@endsection

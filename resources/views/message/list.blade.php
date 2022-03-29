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
                <div class="messages-list" id="messages-list"></div>
            </div>
        </div>
    </div>

    <div class="column is-2"></div>
@endsection

@section('javascript')
    <script>
        window.paginateList = null;

        window.fetchMessageList = function() {
            document.getElementById('messages-list').innerHTML += '<div id="spinner"><i class="fas fa-spinner fa-spin"></i></div>';

            if (document.getElementById('loadmore') !== null) {
                document.getElementById('loadmore').remove();
            }
            
            window.vue.ajaxRequest('get', '{{ url('/messages/list') }}' + ((window.paginateList !== null) ? '?paginate=' + window.paginateList : ''), {}, function(response) {
                if (response.code === 200) {  
                    if (document.getElementById('spinner') !== null) {
                        document.getElementById('spinner').remove();
                    }

                    response.data.forEach(function(elem, index){
                        let html = window.vue.renderMessageListItem(elem);

                        document.getElementById('messages-list').innerHTML += html;
                    });

                    if (response.data.length > 0) {
                        window.paginateList = response.data[response.data.length - 1].updated_at;

                        document.getElementById('messages-list').innerHTML += '<div id="loadmore" class="is-pointer" onclick="window.fetchMessageList();"><br/><center><i class="fas fa-arrow-down is-color-black"></i></center></div>';
                    } else {
                        document.getElementById('messages-list').innerHTML += "<div><br/>{{ __('app.no_more_messages') }}</div>"
                    }
                }
          });
        };

        document.addEventListener('DOMContentLoaded', function() {
           window.fetchMessageList();
        });
    </script>
@endsection

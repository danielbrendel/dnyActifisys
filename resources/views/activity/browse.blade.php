{{--
    ComAct (dnyComAct) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

@extends('layouts.layout_home')

@section('content')
    <div class="column is-1"></div>

    <div class="column is-10 is-default-padding">
        <div class="is-inline-block">
            <h2>{{ __('app.view_activities') }}</h2>
        </div>

        <div class="activity-filter-city is-inline-block">
            <div class="field has-addons">
                <div class="control">
                    <input class="input" type="text" id="inpCityFilter" onkeydown="if (event.keyCode === 13) { document.getElementById('btnFilterCity').click(); }" placeholder="{{ __('app.filter_by_city') }}">
                </div>
                <div class="control">
                    <a id="btnFilterCity" class="button is-info" href="javascript:void(0);" onclick="window.vue.setCityCookieValue(document.getElementById('inpCityFilter').value); location.reload();">
                        {{ __('app.do_filter') }}
                    </a>
                </div>
            </div>
        </div>
        <hr/>
        <br/>
        <br/>

        <div id="activities"></div>
        <div id="loadmore" title="{{ __('app.load_more') }}" class="is-hidden" onclick="fetchActivities()"><center><i class="fas fa-arrow-down is-pointer"></i></center></div>
        <div id="load-spinner"></div>
    </div>

    <div class="column is-1"></div>
@endsection

@section('javascript')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.paginate = null;

            window.city = window.vue.getCityCookieValue();
            if (window.city === '_all') {
                document.getElementById('inpCityFilter').value = '';
            } else {
                document.getElementById('inpCityFilter').value = window.city;
            }

            fetchActivities();
        });

        function fetchActivities()
        {
            document.getElementById('load-spinner').innerHTML = '<center><i class="fas fa-spinner fa-spin"></i></center>';

            window.vue.ajaxRequest('get', '{{ url('/activity/fetch') }}/' + ((window.paginate !== null) ? '?paginate=' + window.paginate : '?paginate=null') + '&city=' + window.city, {}, function(response) {
                if (response.code === 200) {
                    if (response.data.length > 0) {
                        document.getElementById('load-spinner').innerHTML = '';

                        response.data.forEach(function (elem, index) {
                           let html = window.vue.renderActivity(elem);

                            document.getElementById('activities').innerHTML += html;
                            document.getElementById('loadmore').classList.remove('is-hidden');
                        });

                        window.paginate = response.data[response.data.length - 1].date_of_activity;
                    } else {
                        if (response.last) {
                            document.getElementById('loadmore').classList.add('is-hidden');

                            if (document.getElementById('load-spinner') !== null) {
                                document.getElementById('load-spinner').innerHTML = '<center>{{ __('app.no_more_activities') }}</center>';
                            }
                        } else {
                            document.getElementById('loadmore').classList.remove('is-hidden');
                        }
                    }
                }
            });
        }
    </script>
@endsection
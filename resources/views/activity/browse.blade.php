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
    {{ env('APP_DESCRIPTION') }}
@endsection

@section('announcements')
    @include('widgets.announcements', ['announcements' => $announcements])
@endsection

@section('content')
    <div class="column is-1"></div>

    <div class="column is-10 is-default-padding is-def-padding-mobile is-def-color">
        <div class="is-inline-block is-default-side-padding-mobile">
            <h2>{{ __('app.view_activities') }}</h2>
        </div>

        <div>
            <a id="upcoming-activities" href="javascript:void(0);" onclick="window.activityFetchType = 1; this.style.textDecoration = 'underline'; document.getElementById('past-activities').style.textDecoration = 'unset'; window.paginate = null; document.getElementById('loadmore').classList.add('is-hidden'); document.getElementById('activities').innerHTML = ''; fetchActivities();">{{ __('app.upcoming_activities') }}</a>&nbsp;|&nbsp;<a id="past-activities" href="javascript:void(0);" onclick="window.activityFetchType = 0; this.style.textDecoration = 'underline'; document.getElementById('upcoming-activities').style.textDecoration = 'unset'; window.paginate = null; document.getElementById('loadmore').classList.add('is-hidden'); document.getElementById('activities').innerHTML = ''; fetchPastActivities();">{{ __('app.past_activities') }}</a>
        </div>

        <div class="activity-filter is-default-side-padding-mobile">
            <div class="activity-filter-toggle is-def-color" id="activity-filter-action">
                <a href="javascript:void(0);" onclick="document.getElementById('activity-filter-options').classList.toggle('is-hidden'); document.getElementById('activity-filter-action').classList.add('is-hidden'); document.getElementById('activity-divider').classList.toggle('activity-filter-margin');">{{ __('app.filter_options') }}</a>&nbsp;<span onclick="document.getElementById('activity-filter-options').classList.toggle('is-hidden'); document.getElementById('activity-filter-action').classList.add('is-hidden'); document.getElementById('activity-divider').classList.toggle('activity-filter-margin');"><i class="fas fa-chevron-down is-pointer"></i></span>
                <a href="javascript:void(0);" onclick="document.getElementById('activities').innerHTML = ''; window.paginate = null; fetchActivities();" class="is-mobile-refresh"><i class="fas fa-sync-alt"></i></a>
            </div>

            <div class="is-inline-block is-hidden" id="activity-filter-options">
                <div class="field has-addons">
                    <div class="control">
                        <input type="text" id="inpLocationFilter" placeholder="{{ __('app.locations_all') }}" onkeyup="document.getElementById('location-list-content-feed').innerHTML = ''; window.vue.queryLocation(this, 'feed', 'inpLocationFilter');">
                        <div class="dropdown is-left is-inline-block" id="location-list-feed">
                            <div class="dropdown-menu is-color-black-force" role="menu">
                                <div class="dropdown-content" id="location-list-content-feed">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="control">
                        <a id="btnFilterLocation" class="button is-info" href="javascript:void(0);" onclick="window.vue.setLocationCookieValue(document.getElementById('inpLocationFilter').value); location.reload();">
                            {{ __('app.do_filter') }}
                        </a>
                    </div>
                </div>

                <div>
                    <div class="field is-inline-block">
                        <label class="label">{{ __('app.from') }}</label>
                        <div class="control is-inline-block">
                            <input type="date" class="input" id="inpDateFrom">
                        </div>
                    </div>

                    <div class="field is-inline-block">
                        <label class="label">{{ __('app.till') }}</label>
                        <div class="control is-inline-block">
                            <input type="date" class="input" id="inpDateTill">
                        </div>
                    </div>

                    <div class="field is-inline-block">
                        <div class="control is-inline-block is-fixed-filter-button">
                            <a id="btnFilterDate" class="button is-info" href="javascript:void(0);" onclick="window.vue.setDateCookieValue(document.getElementById('inpDateFrom'), document.getElementById('inpDateTill')); location.reload();">
                                {{ __('app.do_filter') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="field has-addons">
                        <div class="control">
                            <input id="inpFilterTag" class="input" type="text" @if (isset($_GET['tag'])) value="{{ $_GET['tag'] }}" @endif onkeydown="if (event.keyCode === 13) { document.getElementById('btnFilterTag').click(); }" placeholder="{{ __('app.filter_by_tag') }}">
                        </div>
                        <div class="control">
                            <a id="btnFilterTag" class="button is-info" href="javascript:void(0);" onclick="location.href = window.location.origin + '/?tag=' + document.getElementById('inpFilterTag').value;">
                                {{ __('app.do_filter') }}
                            </a>
                        </div>
                    </div>
                </div>

                <br/>
                <div>
                    <div class="field has-addons">
                        <div class="control">
                            <select id="inpSelectCategory">
                                <option value="0">{{ __('app.category_all') }}</option>
                                @foreach (\App\CategoryModel::fetch() as $category)
                                    <option value="{{ $category->id }}" @if ((isset($_GET['category'])) && ($_GET['category'] == $category->id)) {{ 'selected' }} @endif>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="control">
                            <a class="button is-info" href="javascript:void(0);" onclick="location.href = window.location.origin + '/?category=' + document.getElementById('inpSelectCategory').value;">
                                {{ __('app.do_filter') }}
                            </a>
                        </div>
                    </div>
                </div>

                <br/>
                <div>
                    <div class="field has-addons">
                        <div class="control">
                            <input id="inpFilterText" class="input" type="text" @if (isset($_GET['text'])) value="{{ $_GET['text'] }}" @endif onkeydown="if (event.keyCode === 13) { document.getElementById('btnFilterText').click(); }" placeholder="{{ __('app.filter_by_text') }}">
                        </div>
                        <div class="control">
                            <a id="btnFilterText" class="button is-info" href="javascript:void(0);" onclick="location.href = window.location.origin + '/?text=' + document.getElementById('inpFilterText').value;">
                                {{ __('app.do_filter') }}
                            </a>
                        </div>
                    </div>
                </div>

                <br/>
                <div>
                    <div class="field">
                        <div class="control">
                            <a id="btnClearFilter" class="button is-info" href="javascript:void(0);" onclick="window.vue.clearFilterCookies(); location.href = '{{ url('/') }}';">
                                {{ __('app.clear_filter') }}
                            </a>
                            &nbsp;
                            <a id="btnCollapseFilter" href="javascript:void(0);" onclick="document.getElementById('activity-filter-options').classList.toggle('is-hidden'); document.getElementById('activity-filter-action').classList.remove('is-hidden'); document.getElementById('activity-divider').classList.toggle('activity-filter-margin');">
                                {{ __('app.collapse_filter') }}
                            </a>
                        </div>
                    </div>
                </div>
                <br/>
            </div>
        </div>
        <hr id="activity-divider"/>

        <div id="activities"></div>
        <div id="loadmore" class="is-hidden"><center><a href="javascript:void(0);" onclick="if (window.activityFetchType) { fetchActivities(); } else { fetchPastActivities(); }">{{ __('app.load_more') }}</a></center></div>
        <div id="load-spinner"></div>
    </div>

    <div class="column is-1"></div>
@endsection

@section('javascript')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.lastActivityId = {{ ((strlen(\App\AppModel::getAdCode()) > 0) ? '2' : '1') }};

            window.paginate = null;
            window.activityFetchType = 1;

            window.locationIdent = window.vue.getLocationCookieValue();
            if (window.locationIdent === '_all') {
                document.getElementById('inpLocationFilter').value = '';
            } else {
                document.getElementById('inpLocationFilter').value = window.locationIdent;
            }

            window.dateFrom = window.vue.getDateFromCookieValue();
            if (window.dateFrom === '_default') {
                document.getElementById('inpDateFrom').value = '';
            } else {
                document.getElementById('inpDateFrom').value = window.dateFrom;
            }

            window.dateTill = window.vue.getDateTillCookieValue();
            if (window.dateTill === '_default') {
                document.getElementById('inpDateTill').value = '';
            } else {
                document.getElementById('inpDateTill').value = window.dateTill;
            }

            fetchActivities();
        });

        function fetchActivities()
        {
            document.getElementById('load-spinner').innerHTML = '<center><i class="fas fa-spinner fa-spin"></i></center>';

            window.vue.ajaxRequest('get', '{{ url('/activity/fetch') }}/' + ((window.paginate !== null) ? '?paginate=' + window.paginate : '?paginate=null') + '&location=' + window.locationIdent + "&date_from=" + window.dateFrom + "&date_till=" + window.dateTill + "<?= ((isset($_GET['tag'])) ? '&tag=' . $_GET['tag'] : '') ?>" + "<?= ((isset($_GET['category'])) ? '&category=' . $_GET['category'] : '') ?>" + "<?= ((isset($_GET['text'])) ? '&text=' . $_GET['text'] : '') ?>", {}, function(response) {
                if (response.code === 200) {
                    if (response.data.length > 0) {
                        document.getElementById('load-spinner').innerHTML = '';

                        response.data.forEach(function (elem, index) {
                           let html = window.vue.renderActivity(elem);

                            document.getElementById('activities').innerHTML += html;
                            document.getElementById('loadmore').classList.remove('is-hidden');
                        });

                        let tagElems = [];
                        let adsNodes = document.getElementsByClassName('activity-ad');
                        if (adsNodes.length > 0) {
                            let childNodes = adsNodes[adsNodes.length - 1].childNodes;
                            for (let i = 0; i < childNodes.length; i++) {
                                if (typeof childNodes[i].tagName !== 'undefined') {
                                    let childTag = document.createElement(childNodes[i].tagName);
                                    let tagCode = document.createTextNode(childNodes[i].innerHTML);
                                    childTag.appendChild(tagCode);
                                    tagElems.push(childTag);
                                }
                            }

                            adsNodes[adsNodes.length - 1].innerHTML = '';

                            for (let i = 0; i < tagElems.length; i++) {
                                adsNodes[adsNodes.length - 1].appendChild(tagElems[i]);
                            }
                        }

                        window.paginate = response.data[response.data.length - window.lastActivityId].date_of_activity_till;
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

        function fetchPastActivities()
        {
            document.getElementById('load-spinner').innerHTML = '<center><i class="fas fa-spinner fa-spin"></i></center>';

            window.vue.ajaxRequest('get', '{{ url('/activity/fetch/past') }}/' + ((window.paginate !== null) ? '?paginate=' + window.paginate : '?paginate=null') + '&location=' + window.locationIdent + "&date_from=" + window.dateFrom + "&date_till=" + window.dateTill + "<?= ((isset($_GET['tag'])) ? '&tag=' . $_GET['tag'] : '') ?>" + "<?= ((isset($_GET['category'])) ? '&category=' . $_GET['category'] : '') ?>" + "<?= ((isset($_GET['text'])) ? '&text=' . $_GET['text'] : '') ?>", {}, function(response) {
                if (response.code === 200) {
                    if (response.data.length > 0) {
                        document.getElementById('load-spinner').innerHTML = '';

                        response.data.forEach(function (elem, index) {
                           let html = window.vue.renderActivity(elem);

                            document.getElementById('activities').innerHTML += html;
                            document.getElementById('loadmore').classList.remove('is-hidden');
                        });

                        let tagElems = [];
                        let adsNodes = document.getElementsByClassName('activity-ad');
                        if (adsNodes.length > 0) {
                            let childNodes = adsNodes[adsNodes.length - 1].childNodes;
                            for (let i = 0; i < childNodes.length; i++) {
                                if (typeof childNodes[i].tagName !== 'undefined') {
                                    let childTag = document.createElement(childNodes[i].tagName);
                                    let tagCode = document.createTextNode(childNodes[i].innerHTML);
                                    childTag.appendChild(tagCode);
                                    tagElems.push(childTag);
                                }
                            }

                            adsNodes[adsNodes.length - 1].innerHTML = '';

                            for (let i = 0; i < tagElems.length; i++) {
                                adsNodes[adsNodes.length - 1].appendChild(tagElems[i]);
                            }
                        }

                        window.paginate = response.data[response.data.length - window.lastActivityId].date_of_activity_till;
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

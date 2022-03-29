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
    {{ $user->name }}
@endsection

@section('content')
    <div class="column is-1"></div>

    <div class="column is-10">
        <div class="columns">
            <div class="column is-12 is-margin-top-20">
                <h3 class="is-color-grey-light">{{ __('app.profile_of', ['name' => $user->name]) }} @if ($user->id === auth()->id()) <div class="is-inline-block is-pointer" title="{{ __('app.settings') }}" onclick="location.href = '{{ url('/settings') }}';"><i class="fas fa-cog"></i></div> @endif</h3>
                <hr/>
            </div>
        </div>

        <div class="columns is-margin-top-20">
            <div class="column is-4">
                <div class="frame">
                    <div class="activity-userdata">
                        <div class="activity-user-top">
                            <div class="is-inline-block">
                                <h1>{{ $user->name }} {!! (($user->verified) ? '&nbsp;<i class="far fa-check-circle" title="Verified user"></i>' : '') !!}</h1>
                            </div>

                            <div class="activity-avatar is-inline-block">
                                <a href="{{ asset('gfx/avatars/' . $user->avatar) }}" target="_blank"><img src="{{ asset('gfx/avatars/' . $user->avatar) }}" alt="avatar"></a>
                            </div>
                        </div>

                        <div class="activity-user-sec">
                            <h2 class="{{ ((strlen($user->bio) === 0) ? 'is-italic' : '') }}">{{ (strlen($user->bio) > 0) ? $user->bio :  __('app.no_information_given') }}</h2>
                        </div>
                    </div>

                    <div class="activity-info">
                        <div><i class="far fa-clock"></i>&nbsp;<span title="{{ $user->created_at  }}">{{ $user->created_at->diffForHumans() }}</span></div>
                        <div><i class="fas fa-map-marker-alt"></i>&nbsp;{{ $user->location }}</div>
                    </div>

                    <div class="activity-info is-margin-top-20">
                        <div class="is-color-lightblue is-underlined"><span>{{ __('app.age') }}:</span> <span class="is-align-right">{{ $user->age }}</span></div>
                        <div class="is-color-lightblue is-underlined"><span>{{ __('app.gender') }}:</span> <span class="is-align-right">{{ $user->genderText }}</span></div>
                        <div class="is-color-lightblue is-underlined"><span>{{ __('app.location') }}:</span> <span class="is-align-right">{{ $user->location }}</span></div>
                        <div class="is-color-lightblue is-underlined"><span>{{ __('app.activities') }}:</span> <span class="is-align-right" id="activity-count">{{ $user->activities }}</span></div>
                    </div>
                </div>

                @if (auth()->id() !== $user->id)
                <div class="windowed-frame is-margin-top-20">
                    <span class="member-actions"><h3 class="is-color-grey">{{ __('app.profile_actions') }}</h3></span>

                    <div class="is-color-lightblue">
                        @if ($user->hasFavorited)
                            <div><a href="{{ url('/user/' . $user->id . '/fav/remove') }}">{{ __('app.remove_favorite') }}</a></div>
                        @else
                            <div><a href="{{ url('/user/' . $user->id . '/fav/add') }}">{{ __('app.add_favorite') }}</a></div>
                        @endif

                        <div><a href="{{ url('/messages/create?userId=' . $user->id) }}">{{ __('app.send_message') }}</a></div>

                        <div><a href="{{ url('/user/' . $user->id . '/report') }}">{{ __('app.report') }}</a></div>

                        @if (!$user->admin)
                            @if ($user->ignored)
                                <div><a href="{{ url('/user/' . $user->id . '/ignore/remove') }}">{{ __('app.remove_from_ignore') }}</a></div>
                            @else
                                <div><a href="{{ url('/user/' . $user->id . '/ignore/add') }}">{{ __('app.add_to_ignore') }}</a></div>
                            @endif
                        @endif

                        @auth
                            @if (\App\User::isAdmin(auth()->id()))
                                <div><a href="javascript:void(0);" onclick="window.vue.lockUser({{ $user->id }});">{{ __('app.lock') }}</a></div>
                            @endif
                        @endauth
                    </div>
                </div>
                @else
                    <div class="windowed-frame is-margin-top-20">
                        <h3 class="is-margin-left-15 is-color-grey">{{ __('app.participating') }}</h3>

                        @if (count($user->actual) > 0)
                        <div>
                            @foreach ($user->actual as $actual)
                                @if ($actual['activityData'] !== null)
                                    <div class="is-color-lightblue is-underlined"><a href="{{ url('/activity/' . $actual['activityData']->slug) }}">{{ $actual['activityData']->title }}</a></div>
                                @endif
                            @endforeach
                        </div>
                        @else
                            <div><i class="is-color-grey">{{ __('app.not_yet_participating') }}</i></div>
                        @endif
                    </div>

                    <div class="windowed-frame is-margin-top-20">
                        <h3 class="is-margin-left-15 is-color-grey">{{ __('app.interested') }}</h3>

                        @if (count($user->potential) > 0)
                            <div>
                                @foreach ($user->potential as $potential)
                                    @if ($potential['activityData'] !== null)
                                        <div class="is-color-lightblue is-underlined"><a href="{{ url('/activity/' . $potential['activityData']->slug) }}">{{ $potential['activityData']->title }}</a></div>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <div><i class="is-color-grey">{{ __('app.not_yet_interested') }}</i></div>
                        @endif
                    </div>
                @endif
            </div>

            <div class="column is-8">
                <div class="activities-nav">
                    <span class="is-color-lightblue"><a href="javascript:void(0);" onclick="document.getElementById('activities-content').innerHTML = ''; window.fetchType = 'running'; window.paginate = null; window.fetchUserActivities();">{{ __('app.running_activities') }}</a></span>&nbsp;|&nbsp;<span class="is-color-lightblue"><a href="javascript:void(0);" onclick="document.getElementById('activities-content').innerHTML = ''; window.fetchType = 'past'; window.paginate = null; window.fetchUserActivities();">{{ __('app.past_activities') }}</a></span>
                </div>

                <div id="activities-content"></div>
            </div>
        </div>
    </div>

    <div class="column is-1"></div>
@endsection

@section('javascript')
    <script>
        window.paginate = null;
        window.fetchType = 'running';
        window.fetchUserActivities = function() {
            document.getElementById('activities-content').innerHTML += '<div id="spinner"><center><i class="fas fa-spinner fa-spin"></i></center></div>';

            window.vue.ajaxRequest('post', '{{ url('/activity/user/' . $user->id) }}', { paginate: window.paginate, type: window.fetchType }, function(response) {
               if (response.code === 200) {
                   if (document.getElementById('spinner')) {
                       document.getElementById('spinner').remove();
                   }

                   if (document.getElementById('loadmore')) {
                       document.getElementById('loadmore').remove();
                   }

                   if (response.data.length > 0) {
                       response.data.forEach(function(elem, index) {
                           elem.user = JSON.parse('<?= json_encode($user_public) ?>');
                           let html = window.vue.renderActivity(elem);

                           document.getElementById('activities-content').innerHTML += html;
                       });

                       window.paginate = response.data[response.data.length - 1].date_of_activity;
                    
                       document.getElementById('activity-count').innerHTML = response.running + '/' + response.total;
                   
                       document.getElementById('activities-content').innerHTML += '<div id="loadmore"><center><br/><span class="is-color-lightblue"><a href="javascript:void(0);" onclick="window.fetchUserActivities();">{{ __('app.load_more') }}</a></span></center></div>';

					   let allActivities = document.getElementsByClassName('activity');
					   for (let i = 0; i < allActivities.length; i++) {
						   allActivities[i].style.left = '-6px';
					   }
				   } else {
                       document.getElementById('activities-content').innerHTML += '<center><i class="is-def-color">{{ __('app.no_more_activities') }}</i></center>';

                       if (document.getElementById('loadmore')) {
                           document.getElementById('loadmore').remove();
                       }
                   }
               }
            });
        };

        document.addEventListener('DOMContentLoaded', function() {
            window.fetchUserActivities('running');
        });
    </script>
@endsection

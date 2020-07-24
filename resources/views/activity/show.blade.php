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
    <div class="column is-2"></div>

    <div class="column is-8 is-default-padding">
        <div class="columns is-margin-top-20">
            <div class="column is-5">
                <div class="frame">
                    <div class="activity-userdata">
                        <div class="activity-user-top">
                            <div class="is-inline-block">
                                <h1>{{ $activity->user->name }}</h1>
                            </div>

                            <div class="activity-avatar is-inline-block">
                                <img src="{{ asset('gfx/avatars/' . $activity->user->avatar) }}" alt="avatar">
                            </div>
                        </div>

                        <div class="activity-user-sec">
                            <h2 class="{{ ((strlen($activity->user->bio) === 0) ? 'is-italic' : '') }}">{{ (strlen($activity->user->bio) > 0) ? $activity->user->bio :  __('app.no_information_given') }}</h2>
                        </div>
                    </div>

                    <div class="activity-info">
                        <div><i class="far fa-clock"></i>&nbsp;<span title="{{ $activity->date_of_activity  }}">{{ $activity->date_of_activity->diffForHumans() }}</span></div>
                        <div><i class="fas fa-map-marker-alt"></i>&nbsp;{{ $activity->location }}</div>
                    </div>
                </div>

                <div class="windowed-frame is-margin-top-20">
                    <span class="activity-participants"><h3>{{ __('app.actual_participants', ['count' => count($activity->actualParticipants)]) }}</h3></span>

                    <div>
                        @if (count($activity->actualParticipants) > 0)
                            @foreach ($activity->actualParticipants as $user)
                                <div class="activity-participant-avatar">
                                    <img src="{{ asset('gfx/avatars/' . $user->user->avatar) }}" class="is-pointer" alt="{{ $user->user->name }}" title="{{ $user->user->name }}" onclick="window.open('{{ url('/user/' . $user->user->id) }}');"/>
                                </div>
                            @endforeach
                        @else
                            <i>{{ __('app.no_participants') }}</i>
                        @endif
                    </div>
                </div>

                <div class="windowed-frame is-margin-top-20">
                    <span class="activity-potential"><h3>{{ __('app.potential_participants', ['count' => count($activity->potentialParticipants)]) }}</h3></span>

                    <div>
                        @if (count($activity->potentialParticipants) > 0)
                            @foreach ($activity->potentialParticipants as $user)
                                <div class="activity-participant-avatar">
                                    <img src="{{ asset('gfx/avatars/' . $user->user->avatar) }}" class="is-pointer" alt="{{ $user->user->name }}" title="{{ $user->user->name }}" onclick="window.open('{{ url('/user/' . $user->user->id) }}');"/>
                                </div>
                            @endforeach
                        @else
                            <i>{{ __('app.no_one_interested') }}</i>
                        @endif
                    </div>
                </div>
            </div>

            <div class="column is-7">
                <div class="activity-title">
                    <div class="activity-headline is-inline-block">
                        {{ $activity->title }}
                    </div>

                    <div class="activity-options is-inline-block">
                        <div class="dropdown is-right" id="activity-options-{{ $activity->id }}">
                            <div class="dropdown-trigger">
                                <i class="fas fa-ellipsis-v is-pointer" onclick="window.vue.toggleActivityOptions(document.getElementById('activity-options-{{ $activity->id }}'));"></i>
                            </div>
                            <div class="dropdown-menu" role="menu">
                                <div class="dropdown-content">
                                    <a onclick="window.vue.toggleActivityOptions(document.getElementById('activity-options-{{ $activity->id }}'));" href="whatsapp://send?text={{ url('/activity/' . $activity->id) }} - {{ $activity->title }}" class="dropdown-item">
                                        <i class="far fa-copy"></i>&nbsp;{{ __('app.share_whatsapp') }}
                                    </a>
                                    <a onclick="window.vue.toggleActivityOptions(document.getElementById('activity-options-{{ $activity->id }}'));" href="https://twitter.com/share?url={{ urlencode(url('/activity/' . $activity->id)) }}&text={{ $activity->title }}" class="dropdown-item">
                                        <i class="fab fa-twitter"></i>&nbsp;{{ __('app.share_twitter') }}
                                    </a>
                                    <a onclick="window.vue.toggleActivityOptions(document.getElementById('activity-options-{{ $activity->id }}'));" href="https://www.facebook.com/sharer/sharer.php?u={{ url('/activity/' . $activity->id) }}" class="dropdown-item">
                                        <i class="fab fa-facebook"></i>&nbsp;{{ __('app.share_facebook') }}
                                    </a>
                                    <a onclick="window.vue.toggleActivityOptions(document.getElementById('activity-options-{{ $activity->id }}'));" href="mailto:name@domain.com?body={{ url('/activity/' . $activity->id) }} - {{ $activity->title }}" class="dropdown-item">
                                        <i class="far fa-envelope"></i>&nbsp;{{ __('app.share_email') }}
                                    </a>
                                    <a onclick="window.vue.toggleActivityOptions(document.getElementById('activity-options-{{ $activity->id }}'));" href="sms:000000000?body={{ url('/activity/' . $activity->id) }} - {{ $activity->title }}" class="dropdown-item">
                                        <i class="fas fa-sms"></i>&nbsp;{{ __('app.share_sms') }}
                                    </a>
                                    <a href="javascript:void(0)" onclick="window.vue.copyToClipboard('{{ url('/activity/' . $activity->id) }} - {{ $activity->title }}'); window.vue.toggleActivityOptions(document.getElementById('activity-options-{{ $activity->id }}'));" class="dropdown-item">
                                        <i class="far fa-copy"></i>&nbsp;{{ __('app.share_clipboard') }}
                                    </a>

                                    <hr class="dropdown-divider">

                                    @auth
                                        @if (\App\User::isAdmin(auth()->id()) || $activity->owner === auth()->id())
                                            <a href="javascript:void(0)" onclick="window.vue.cancelActivity({{ $activity->id }}); window.vue.toggleActivityOptions(document.getElementById('activity-options-{{ $activity->id }}'));" class="dropdown-item">
                                                {{ __('app.cancel') }}
                                            </a>

                                            <a href="javascript:void(0)" onclick="window.vue.lockActivity({{ $activity->id }}); window.vue.toggleActivityOptions(document.getElementById('activity-options-{{ $activity->id }}'));" class="dropdown-item">
                                                {{ __('app.lock') }}
                                            </a>
                                        @endif
                                    @endauth
                                    <a href="javascript:void(0)" onclick="window.vue.reportActivity({{ $activity->id }}); window.vue.toggleActivityOptions(document.getElementById('activity-options-{{ $activity->id }}'));" class="dropdown-item">
                                        {{ __('app.report') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="activity-description">
                    {{ $activity->description }}
                </div>

                <div class="activity-buttons">
                    <div class="buttons-left is-inline-block">
                        <a class="button is-outlined">{{ __('app.report') }}</a>
                    </div>

                    <div class="buttons-right is-inline-block">
                        <div class="is-inline-block"><button type="button" class="button is-success">{{ __('app.participate') }}</button></div>
                        <div class="is-inline-block"><button type="button" class="button is-info is-outlined">{{ __('app.interested') }}</button></div>
                    </div>
                </div>

                <hr/>

                <div>
                    @auth
                        <div class="thread-input-avatar is-inline-block">
                            <img src="{{ asset('gfx/avatars/' . \App\User::get(auth()->id())->avatar) }}">
                        </div>

                        <div class="thread-input-form is-inline-block">
                            <form method="POST" action="{{ url('/activity/' . $activity->id . '/thread/add') }}">
                                @csrf

                                <div class="thread-input-form-text"><textarea name="message" placeholder="{{ __('app.type_a_message') }}"></textarea></div>
                                <div class="thread-input-form-button"><input type="submit" class="button is-link" value="{{ __('app.send') }}"/></div>
                            </form>
                        </div>
                    @endauth
                </div>

                <div>
                    <a name="thread"></a>
                    <div id="thread"></div>
                    <div id="loading" style="display: none;"><center><i class="fas fa-spinner fa-spin"></i></center></div>
                    <div id="loadmore" style="display: none;"><center><i class="fas fa-arrow-down is-pointer" onclick="fetchThread()"></i></center></div>
                </div>
            </div>
        </div>
    </div>

    <div class="column is-2"></div>
@endsection

@section('javascript')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.paginate = null;
            fetchThread();
        });

        function fetchThread()
        {
            document.getElementById('loading').style.display = 'block';
            document.getElementById('loadmore').style.display = 'none';

            window.vue.ajaxRequest('get', '{{ url('/activity/' . $activity->id . '/thread') }}' + ((window.paginate !== null) ? '?paginate=' + window.paginate : ''), {}, function(response){
                if (response.code == 200) {
                    if (response.data.length > 0) {
                        response.data.forEach(function (elem, index) {
                            let insertHtml = window.vue.renderThread(elem, elem.adminOrOwner);
                            document.getElementById('thread').innerHTML += insertHtml;
                        });

                        window.paginate = response.data[response.data.length - 1].id;

                        document.getElementById('loading').style.display = 'none';
                        document.getElementById('loadmore').style.display = 'block';

                        if (response.last) {
                            document.getElementById('loading').innerHTML = '<br/><br/><center><i class="is-color-grey">{{ __('app.no_more_comments')  }}</i></center>';
                        }
                    } else {
                        if (window.paginate === null) {
                            document.getElementById('loading').innerHTML = '<br/><br/><center><i class="is-color-grey">{{ __('app.no_comments_yet')  }}</i></center>';
                        } else {
                            if (document.getElementById('no-more-comments') == null) {
                                document.getElementById('thread').innerHTML += '<div id="no-more-comments"><br/><br/><center><i>{{ __('app.no_more_comments') }}</i></center><br/></div>';
                            }
                        }
                    }
                }
            });
        }
    </script>
@endsection

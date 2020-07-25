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

    <div class="column is-8">
        <div class="columns is-margin-top-20">
            <div class="column is-5">
                <div class="frame">
                    <div class="activity-userdata">
                        <div class="activity-user-top">
                            <div class="is-inline-block">
                                <h1>{{ $user->name }}</a></h1>
                            </div>

                            <div class="activity-avatar is-inline-block">
                                <img src="{{ asset('gfx/avatars/' . $user->avatar) }}" alt="avatar">
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
                </div>

                <div class="windowed-frame is-margin-top-20">
                    <span class="member-actions"><h3>{{ __('app.profile_actions') }}</h3></span>

                    <div>
                        <div><a href="{{ url('/member/' . $user->id . '/add') }}">{{ __('app.add_favorite') }}</a></div>
                        <div><a href="{{ url('/messages/create?userId=' . $user->id) }}">{{ __('app.send_message') }}</a></div>
                        <div><a href="{{ url('/member/' . $user->id . '/report') }}">{{ __('app.report') }}</a></div>

                        @if ((!$user->admin) || ($user->id !== auth()->id()))
                            @if ($user->ignored)
                                <div><a href="{{ url('/member/' . $user->id . '/ignore/remove') }}">{{ __('app.remove_from_ignore') }}</a></div>
                            @else
                                <div><a href="{{ url('/member/' . $user->id . '/ignore/add') }}">{{ __('app.add_to_ignore') }}</a></div>
                            @endif
                        @endif

                        @auth
                            @if (\App\User::isAdmin(auth()->id()))
                                <div><a href="javascript:void(0);" onclick="window.vue.lockUser({{ $user->id }});">{{ __('app.lock') }}</a></div>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>

            <div class="column is-7">
                <h3>{{ __('app.profile_of', ['name' => $user->name]) }}</h3>
                <hr/>
                <br/>

                <div>
                    <div class="is-profile-info-item"><strong>{{ __('app.name') }}</strong>: {{ $user->name }}</div>
                    <div class="is-profile-info-item"><strong>{{ __('app.age') }}</strong>: {{ $user->age }}</div>
                    <div class="is-profile-info-item"><strong>{{ __('app.gender') }}</strong>: {{ $user->genderText }}</div>
                    <div class="is-profile-info-item"><strong>{{ __('app.location') }}</strong>: {{ $user->location }}</div>
                    <div class="is-profile-info-item"><strong>{{ __('app.activities') }}</strong>: {{ $user->activities }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="column is-2"></div>
@endsection

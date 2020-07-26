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
                                <h1><a href="{{ url('/user/' . $activity->user->id) }}">{{ $activity->user->name }}</a></h1>
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
                        <div>&nbsp;<i class="far fa-clock"></i>&nbsp;<span title="{{ $activity->date_of_activity  }}">{{ $activity->date_of_activity->diffForHumans() }}</span></div>
                        <div class="is-capitalized">&nbsp;<i class="fas fa-map-marker-alt"></i>&nbsp;&nbsp;{{ $activity->location }}</div>
                        <div class="is-capitalized"><i class="fas fa-users"></i>&nbsp;{{ (($activity->limit === 0) ? __('app.no_limit') : __('app.limit_count', ['count' => $activity->limit])) }}</div>
                    </div>

                    @if ($activity->canceled)
                        <div class="activity-canceled">
                            {{ __('app.activity_canceled_title') }}
                        </div>
                    @endif

                    @if ((new DateTime('now')) > (new DateTime($activity->date_of_activity)))
                        <div class="activity-expired">
                            {{ __('app.activity_expired') }}
                        </div>
                    @endif

                    @if (($activity->limit !== 0) && (count($activity->actualParticipants) >= $activity->limit))
                        <div class="activity-limit-reached">
                            {{ __('app.participant_limit_reached_short') }}
                        </div>
                    @endif
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
                    <div class="activity-headline is-inline-block is-breakall">
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
                                            <a href="javascript:void(0)" onclick="window.vue.bShowEditActivity = true; window.vue.toggleActivityOptions(document.getElementById('activity-options-{{ $activity->id }}'));" class="dropdown-item">
                                                {{ __('app.edit') }}
                                            </a>

                                            <a href="javascript:void(0)" onclick="window.vue.bShowCancelActivity = true; window.vue.toggleActivityOptions(document.getElementById('activity-options-{{ $activity->id }}'));" class="dropdown-item">
                                                {{ __('app.cancel') }}
                                            </a>

                                            <a href="javascript:void(0)" onclick="window.vue.lockActivity({{ $activity->id }}); window.vue.toggleActivityOptions(document.getElementById('activity-options-{{ $activity->id }}'));" class="dropdown-item">
                                                {{ __('app.lock') }}
                                            </a>
                                        @endif
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="activity-description is-breakall">
                    {{ $activity->description }}
                </div>

                @auth
                    <div class="activity-buttons">
                        <div class="buttons-left is-inline-block">
                            <a class="button is-outlined" href="{{ url('/activity/' . $activity->id . '/report') }}">{{ __('app.report') }}</a>
                        </div>

                        <div class="buttons-right is-inline-block">
                            @if ((new DateTime('now')) < (new DateTime($activity->date_of_activity)))
                                @if ($activity->owner !== auth()->id())
                                    @if (!$activity->selfParticipated)
                                        <div class="is-inline-block"><button type="button" id="btnParticipate" class="button is-success" onclick="location.href = '{{ url('/activity/' . $activity->id . '/participant/add') }}';">{{ __('app.participate') }}</button></div>
                                    @else
                                        <div class="is-inline-block"><button type="button" id="btnParticipate" class="button is-success is-outlined" onclick="location.href = '{{ url('/activity/' . $activity->id . '/participant/remove') }}';">{{ __('app.not_participate') }}</button></div>
                                    @endif
                                @endif

                                @if (!$activity->selfParticipated)
                                    @if (!$activity->selfInterested)
                                        <div class="is-inline-block"><button type="button" id="btnPotential" class="button is-info is-outlined" onclick="location.href = '{{ url('/activity/' . $activity->id . '/interested/add') }}';">{{ __('app.interested') }}</button></div>
                                    @else
                                        <div class="is-inline-block"><button type="button" id="btnPotential" class="button is-info is-outlined" onclick="location.href = '{{ url('/activity/' . $activity->id . '/interested/remove') }}';">{{ __('app.not_interested') }}</button></div>
                                    @endif
                                @endif
                            @endif
                        </div>
                    </div>
                @endauth

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

    <div class="modal" :class="{'is-active': bShowEditActivity}">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head is-stretched">
                <p class="modal-card-title">{{ __('app.edit_activity') }}</p>
                <button class="delete" aria-label="close" onclick="vue.bShowEditActivity = false;"></button>
            </header>
            <section class="modal-card-body is-stretched">
                <form id="frmEditActivity" method="POST" action="{{ url('/activity/edit') }}">
                    @csrf

                    <input type="hidden" name="activityId" value="{{ $activity->id }}"/>

                    <div class="field">
                        <label class="label">{{ __('app.title') }}</label>
                        <div class="control">
                            <input id="caTitle" class="input" type="text" name="title" value="{{ $activity->title }}" onkeyup="window.vue.invalidCreateActivity();" onchange="window.vue.invalidCreateActivity();" required>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label">{{ __('app.description') }}</label>
                        <div class="control">
                            <textarea id="caDescription" name="description" onkeyup="window.vue.invalidCreateActivity();" onchange="window.vue.invalidCreateActivity();" required>{{ $activity->description }}</textarea>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label">{{ __('app.date') }}</label>
                        <div class="control">
                            <input id="caDate" class="input" type="date" name="date_of_activity" value="{{ date('Y-m-d', strtotime($activity->date_of_activity)) }}" onkeyup="window.vue.invalidCreateActivity();" onchange="window.vue.invalidCreateActivity();" required>
                        </div>
                        <p class="help is-danger is-hidden" id="activity-date-hint">{{ __('app.date_is_in_past') }}</p>
                    </div>

                    <div class="field">
                        <label class="label">{{ __('app.time') }}</label>
                        <div class="control">
                            <input id="caTime" class="input" type="time" name="time_of_activity" value="{{ date('H:i', strtotime($activity->date_of_activity)) }}" onkeyup="window.vue.invalidCreateActivity();" onchange="window.vue.invalidCreateActivity();" required>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label">{{ __('app.location') }}</label>
                        <div class="control">
                            <input id="caLocation" class="input" type="text" name="location" value="{{ $activity->location }}" onkeyup="window.vue.invalidCreateActivity();" onchange="window.vue.invalidCreateActivity();" required>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label">{{ __('app.limit') }}</label>
                        <div class="control">
                            <input class="input" type="number" name="limit" value="{{ $activity->limit }}" min="0">
                        </div>
                    </div>
                </form>
            </section>
            <footer class="modal-card-foot is-stretched">
                <span>
                    <button id="btnCreateActivity" class="button is-success" onclick="if (!this.disabled) { document.getElementById('frmEditActivity').submit(); }">{{ __('app.save') }}</button>
                </span>
            </footer>
        </div>
    </div>

    <div class="modal" :class="{'is-active': bShowCancelActivity}">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head is-stretched">
                <p class="modal-card-title">{{ __('app.cancel_activity') }}</p>
                <button class="delete" aria-label="close" onclick="vue.bShowCancelActivity = false;"></button>
            </header>
            <section class="modal-card-body is-stretched">
                <form id="frmCancelActivity" method="POST" action="{{ url('/activity/' . $activity->id . '/cancel') }}">
                    @csrf

                    <div class="field">
                        <label>{{ __('app.confirm_cancel_activity') }}</label>
                    </div>

                    <div class="field">
                        <label class="label">{{ __('app.cancel_activity_reason') }}</label>
                        <div class="control">
                            <textarea name="reason" placeholder="{{ __('app.type_something') }}"></textarea>
                        </div>
                    </div>
                </form>
            </section>
            <footer class="modal-card-foot is-stretched">
                <button class="button is-danger" onclick="document.getElementById('frmCancelActivity').submit();">{{ __('app.cancel') }}</button>
                <button class="button" onclick="vue.bShowCancelActivity = false;">{{ __('app.close') }}</button>
            </footer>
        </div>
    </div>

    <div class="modal" :class="{'is-active': bShowActivityCanceled}">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head is-stretched">
                <p class="modal-card-title">{{ __('app.activity_canceled_title') }}</p>
                <button class="delete" aria-label="close" onclick="vue.bShowActivityCanceled = false;"></button>
            </header>
            <section class="modal-card-body is-stretched">
                {{ __('app.activity_canceled_message') }}
                <br/>
                <br/>
                {{ __('app.reason') }}: {{ ((strlen($activity->cancelReason) > 0) ? $activity->cancelReason : __('app.no_reason_specified')) }}
            </section>
            <footer class="modal-card-foot is-stretched">
                <button class="button" onclick="vue.bShowActivityCanceled = false;">{{ __('app.close') }}</button>
            </footer>
        </div>
    </div>

    <div class="modal" :class="{'is-active': bShowActivityExpired}">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head is-stretched">
                <p class="modal-card-title">{{ __('app.activity_expired') }}</p>
                <button class="delete" aria-label="close" onclick="vue.bShowActivityExpired = false;"></button>
            </header>
            <section class="modal-card-body is-stretched">
                {{ __('app.activity_expired_message') }}
            </section>
            <footer class="modal-card-foot is-stretched">
                <button class="button" onclick="vue.bShowActivityExpired = false;">{{ __('app.close') }}</button>
            </footer>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.paginate = null;
            fetchThread();

            @if ($activity->canceled)
                try {
                    document.getElementById('btnPotential').disabled = true;
                    document.getElementById('btnParticipate').disabled = true;
                } catch (e) {
                }

                window.vue.bShowActivityCanceled = true;
            @elseif ((new DateTime('now')) > (new DateTime($activity->date_of_activity)))
                window.vue.bShowActivityExpired = true;
            @endif
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
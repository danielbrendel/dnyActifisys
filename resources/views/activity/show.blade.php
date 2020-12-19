{{--
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

@extends('layouts.layout_home')

@section('title')
    {{ $activity->title }}
@endsection

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
                                <a href="{{ asset('gfx/avatars/' . $activity->user->avatar) }}" target="_blank"><img src="{{ asset('gfx/avatars/' . $activity->user->avatar) }}" alt="avatar"></a>
                            </div>
                        </div>

                        <div class="activity-user-sec">
                            <h2 class="{{ ((strlen($activity->user->bio) === 0) ? 'is-italic' : '') }}">{{ (strlen($activity->user->bio) > 0) ? $activity->user->bio :  __('app.no_information_given') }}</h2>
                        </div>
                    </div>

                    <div class="activity-info">
                        <div>&nbsp;<i class="far fa-clock"></i>&nbsp;<span title="{{ $activity->date_of_activity->diffForHumans()  }}">{{ $activity->date_of_activity_display }}</span></div>
                        <div class="is-capitalized">&nbsp;<i class="fas fa-map-marker-alt"></i>&nbsp;&nbsp;{{ $activity->location }}</div>
                        <div class="is-capitalized"><i class="fas fa-users"></i>&nbsp;{{ (($activity->limit === 0) ? __('app.no_limit') : __('app.limit_count', ['count' => $activity->limit])) }}</div>
                        <div><i class="fas fa-th-list"></i>&nbsp;@if ($activity->category === 0) {{ __('app.category_zero') }} @else {{ $activity->categoryData->name }} @endif</div>
                        @if ($activity->only_gender !== 0)
                            <div>
                                <i class="fas fa-ban"></i>&nbsp;{{ __('app.gender_restricted') }}
                                (
                                    @if ($activity->only_gender === 1)
                                        {{ __('app.gender_male') }}
                                    @elseif ($activity->only_gender === 2)
                                        {{ __('app.gender_female') }}
                                    @elseif ($activity->only_gender === 3)
                                        {{ __('app.gender_diverse') }}
                                    @endif
                                )
                            </div>
                        @endif
                        @if ($activity->only_verified)
                            <div><i class="far fa-check-circle"></i>&nbsp;{{ __('app.only_verified') }}</div>
                        @endif
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
                            <i class="is-color-grey">{{ __('app.no_participants') }}</i>
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
                            <i class="is-color-grey">{{ __('app.no_one_interested') }}</i>
                        @endif
                    </div>
                </div>

                @if ($activity->selfParticipated)
                    <div class="windowed-frame is-margin-top-20">
                        <span class="activity-potential"><h3>{{ __('app.images') }} </h3></span>

                        <div>
                            @foreach ($activity->images as $image)
                                <div class="activity-image-item">
                                    <div class="is-breakall is-inline-block"><i class="fas fa-image"></i>&nbsp;<a class="is-color-lightblue" href="{{ asset('gfx/uploads/' . $image->file) }}" target="_blank">{{ $image->name }}</a></div>
                                    @if ($image->owner === auth()->id())
                                        <div class="is-inline-block is-pointer float-right" title="{{ __('app.remove') }}" onclick="if (confirm('{{ __('app.confirm_file_delete') }}')) { location.href = '{{ url('/file/' . $image->id . '/delete') }}'; }"><i class="fas fa-times"></i>&nbsp;&nbsp;</div>
                                    @endif
                                </div>
                            @endforeach

                            <br/>
                            <span class="is-inline-block is-pointer" title="{{ __('app.upload_image') }}" onclick="window.vue.bShowUploadImage = true;"><i class="fas fa-plus is-color-green icon-large"></i></span>
                        </div>
                    </div>
                @endif
            </div>

            <div class="column is-7">
                <div class="activity-title">
                    <div class="activity-headline is-inline-block is-breakall @if ($activity->canceled) is-striked @endif">
                        <a name="title"></a>
                        {{ $activity->title }}
                        <a href="javascript:void(0);" onclick="location.href = '{{ url('/activity/' . $activity->slug . '/refresh') }}';" class="is-mobile-refresh2"><i class="fas fa-sync-alt"></i></a>
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

                                    @auth
                                        @if (\App\User::isAdmin(auth()->id()) || $activity->owner === auth()->id())
                                            <hr class="dropdown-divider">

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

                <div class="activity-tags">
                    @foreach (explode(' ', $activity->tags) as $tag)
                        @if ($tag !== '')
                            <div class="activity-tag">
                                <span><a href="{{ url('/?tag=' . $tag) }}">#{{ $tag }}</a></span>
                            </div>
                        @endif
                    @endforeach
                </div>

                <div class="activity-buttons">
                    <div class="buttons-left is-inline-block">
                        <a class="button is-outlined" href="@auth {{ url('/activity/' . $activity->id . '/report') }} @elseguest {{ 'javascript:void(0);' }} @endauth" @guest onclick="window.vue.bShowLogin = true;" @endguest>{{ __('app.report') }}</a>
                    </div>

                    <div class="buttons-right is-inline-block">
                        @if ((new DateTime('now')) < (new DateTime(date('Y-m-d H:i:s', strtotime($activity->date_of_activity)))))
                            @auth 
                                @if ($activity->owner !== auth()->id())
                                    @if (!$activity->selfParticipated)
                                        <div class="is-inline-block"><button type="button" id="btnParticipate" class="button is-success" onclick="location.href = '{{ url('/activity/' . $activity->id . '/participant/add') }}';">{{ __('app.participate') }}</button></div>
                                    @else
                                        <div class="is-inline-block"><button type="button" id="btnParticipate" class="button is-danger is-outlined" onclick="location.href = '{{ url('/activity/' . $activity->id . '/participant/remove') }}';">{{ __('app.not_participate') }}</button></div>
                                    @endif
                                @endif
                            @elseguest
                                <div class="is-inline-block"><button type="button" id="btnParticipate" class="button is-success" onclick="window.vue.bShowLogin = true;">{{ __('app.participate') }}</button></div>
                            @endauth

                            @auth
                                @if (!$activity->selfParticipated)
                                    @if (!$activity->selfInterested)
                                        <div class="is-inline-block"><button type="button" id="btnPotential" class="button is-info is-outlined" onclick="location.href = '{{ url('/activity/' . $activity->id . '/interested/add') }}';">{{ __('app.interested') }}</button></div>
                                    @else
                                        <div class="is-inline-block"><button type="button" id="btnPotential" class="button is-info is-outlined" onclick="location.href = '{{ url('/activity/' . $activity->id . '/interested/remove') }}';">{{ __('app.not_interested') }}</button></div>
                                    @endif
                                @endif
                            @elseguest
                                <div class="is-inline-block"><button type="button" id="btnPotential" class="button is-info is-outlined" onclick="window.vue.bShowLogin = true;">{{ __('app.interested') }}</button></div> 
                            @endauth
                        @endif
                    </div>
                </div>

                <hr/>

                <div>
                    <div class="thread-input-avatar is-inline-block">
                        <img src="{{ asset('gfx/avatars/' . ((auth()->id() != null) ? \App\User::get(auth()->id())->avatar : 'default.png' )) }}">
                    </div>

                    <div class="thread-input-form is-inline-block is-def-color">
                        <form method="POST" action="{{ url('/activity/' . $activity->id . '/thread/add') }}">
                            @csrf

                            <div class="thread-input-form-text">
                                <textarea name="message" placeholder="{{ __('app.type_a_message') }}"></textarea>
                            </div>

                            <div class="thread-input-form-button">
                                @auth
                                    <input type="submit" class="button is-link" value="{{ __('app.send') }}"/>
                                @elseguest
                                    <button type="button" class="button is-link" onclick="window.vue.bShowLogin = true;">{{ __('app.send') }}</button> 
                                @endauth
                            </div>
                        </form>
                    </div>
                </div>

                <div>
                    <a name="thread"></a>
                    <div id="loading" style="display: none;"><center><i class="fas fa-spinner fa-spin"></i></center></div>
                    <div id="loadmore" style="display: none;"><center><a class="is-color-grey is-underlined2" href="javascript:void(0);" onclick="fetchThread()">{{ __('app.load_older_posts') }}</a></center></div>
                    <div id="thread"></div>
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
                        <label class="label">{{ __('app.category') }}</label>
                        <div class="control">
                            <select name="category">
                                @foreach (\App\CategoryModel::fetch() as $category)
                                    <option value="{{ $category->id }}" @if ($category->id === $activity->category) {{ 'selected' }} @endif>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label">{{ __('app.location') }}</label>
                        <div class="control">
                            <select class="input" name="location" id="caLocation">
                                @foreach (\App\LocationModel::fetch() as $location)
                                    <option value="{{ $location->name }}">{{ ucfirst($location->name) }}</option>
                                @endforeach 
                            </select>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label">{{ __('app.limit') }}</label>
                        <div class="control">
                            <input class="input" type="number" name="limit" value="{{ $activity->limit }}" min="0">
                        </div>
                    </div>

                    <div class="field">
                        <label class="label">{{ __('app.only_gender') }}</label>
                        <div class="control">
                            <select name="gender">
                                <option value="0">{{ __('app.all') }}</option>
                                <option value="1">{{ __('app.gender_male') }}</option>
                                <option value="2">{{ __('app.gender_female') }}</option>
                                <option value="3">{{ __('app.gender_diverse') }}</option>
                            </select>
                        </div>
                    </div>

                    @if (\App\VerifyModel::getState(auth()->id()) == \App\VerifyModel::STATE_VERIFIED)
                    <div class="field">
                        <label class="label">{{ __('app.only_verified') }}</label>
                        <div class="control">
                            <input type="checkbox" data-role="checkbox" data-type="2" data-caption="{{ __('app.only_verified_long') }}" name="only_verified" value="1" @if ($activity->only_verified) {{ 'checked' }} @endif>
                        </div>
                    </div>
                    @endif
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

    <div class="modal" :class="{'is-active': bShowUploadImage}">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head is-stretched">
                <p class="modal-card-title">{{ __('app.image_upload') }}</p>
                <button class="delete" aria-label="close" onclick="vue.bShowUploadImage = false;"></button>
            </header>
            <section class="modal-card-body is-stretched">
                <form id="frmFileUpload" method="POST" action="{{ url('/activity/' . $activity->id . '/upload') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="field">
                        <div class="control">
                            <input type="file" name="image" data-role="file" data-type="2"/>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label">{{ __('app.name') }}</label>
                        <div class="control">
                            <input type="text" name="name"/>
                        </div>
                    </div>
                </form>
            </section>
            <footer class="modal-card-foot is-stretched">
                <button class="button is-success" onclick="document.getElementById('frmFileUpload').submit();">{{ __('app.upload') }}</button>
                <button class="button" onclick="vue.bShowUploadImage = false;">{{ __('app.close') }}</button>
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

            @auth
                window.isAuth = true;
            @elseguest
                window.isAuth = false;
            @endauth

            window.vue.ajaxRequest('get', '{{ url('/activity/' . $activity->id . '/thread') }}' + ((window.paginate !== null) ? '?paginate=' + window.paginate : ''), {}, function(response){
                if (response.code == 200) {
                    if (response.data.length > 0) {
                        response.data.forEach(function (elem, index) {
                            let insertHtml = window.vue.renderThread(elem, elem.adminOrOwner, false, 0, window.isAuth);
                            document.getElementById('thread').innerHTML = insertHtml + document.getElementById('thread').innerHTML;
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
                                document.getElementById('thread').innerHTML = '<div id="no-more-comments"><br/><br/><center><i class="is-color-grey">{{ __('app.no_more_comments') }}</i></center><br/></div>' + document.getElementById('thread').innerHTML;
                                document.getElementById('loading').style.display = 'none';
                            }
                        }
                    }
                }
            });
        }
    </script>
@endsection

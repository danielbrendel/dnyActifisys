{{--
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2022 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

@extends('layouts.layout_home')

@section('title', env('APP_PROJECTNAME') . ' - ' . $thread->title . ' - ' . __('app.thread'))

@section('content')
    <div class="column is-2"></div>

    <div class="column is-8 fixed-form" id="feed-left">
        <div class="is-default-padding">
            <h1>{{ $thread->title }} @if ($thread->sticky) <i class="fas fa-thumbtack"></i> @endif @if ($thread->locked) <i class="fas fa-lock"></i> @endif</h1>

            <div>
                <div class="is-inline-block"><a href="{{ url('/user/' . $thread->owner->id) }}"><img class="is-round" src="{{ asset('gfx/avatars/' . $thread->owner->avatar) }}" width="32" height="32" alt="avatar"/></a></div>
                <div class="is-inline-block"><a href="{{ url('/user/' . $thread->owner->id) }}">{{ $thread->owner->name }}</a></div>
            </div>
        </div>

        <div class="is-default-padding">
            @if (($user) && (($user->admin) || ($user->maintainer)))
                <a href="javascript:void(0);" onclick="window.vue.bShowEditForumThread = true;">{{ __('app.edit_thread') }}</a><br/>
            @endif
        </div>

        <div class="is-default-padding field">
            <div class="control">
                <button class="button is-link" onclick="document.getElementById('threadId').value = '{{ $thread->id }}'; window.vue.bShowReplyForumThread = true;" @if ($thread->locked) title="{{ __('app.no_reply_to_locked_thread') }}" disabled @endif>{{ __('app.reply') }}</button>&nbsp;<button class="button float-right" onclick="location.href = '{{ url('/forum/' . $thread->forumId . '/show') }}';">{{ __('app.go_back') }}</button>
            </div>
        </div>

        <div id="postings"></div>
    </div>

    <div class="column is-2"></div>
@endsection

@section('modal')
    <div class="modal" :class="{'is-active': bShowReplyForumThread}">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head is-stretched">
                <p class="modal-card-title">{{ __('app.reply_thread') }}</p>
                <button class="delete" aria-label="close" onclick="vue.bShowReplyForumThread = false;"></button>
            </header>
            <section class="modal-card-body is-stretched">
                <form id="formReplyForumThread" method="POST" action="{{ url('/forum/thread/reply') }}">
                    @csrf

                    <input type="hidden" id="threadId" name="id">

                    <div class="field">
                        <label class="label">{{ __('app.message') }}</label>
                        <div class="control">
                            <textarea id="reply-forum-thread-post" name="message" placeholder="{{ __('app.enter_message') }}"></textarea>
                        </div>
                    </div>
                </form>
            </section>
            <footer class="modal-card-foot is-stretched">
                <button class="button is-success" onclick="document.getElementById('formReplyForumThread').submit();">{{ __('app.reply') }}</button>
                <button class="button" onclick="vue.bShowReplyForumThread = false;">{{ __('app.cancel') }}</button>
            </footer>
        </div>
    </div>

    <div class="modal" :class="{'is-active': bShowEditForumThread}">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head is-stretched">
                <p class="modal-card-title">{{ __('app.edit_thread') }}</p>
                <button class="delete" aria-label="close" onclick="vue.bShowEditForumThread = false;"></button>
            </header>
            <section class="modal-card-body is-stretched">
                <form id="formEditForumThread" method="POST" action="{{ url('/forum/thread/edit') }}">
                    @csrf

                    <input type="hidden" id="threadId" name="id" value="{{ $thread->id }}">

                    <div class="field">
                        <label class="label">{{ __('app.title') }}</label>
                        <div class="control">
                            <input type="text" name="title" value="{{ $thread->title }}">
                        </div>
                    </div>

                    <div class="field">
                        <div class="control">
                            <input type="checkbox" name="sticky" data-role="checkbox" data-style="2" data-caption="{{ __('app.set_thread_sticky') }}" value="1" @if ($thread->sticky) {{ 'checked' }} @endif>
                        </div>
                    </div>

                    <div class="field">
                        <div class="control">
                            <input type="checkbox" name="locked" data-role="checkbox" data-style="2" data-caption="{{ __('app.set_thread_locked') }}" value="1" @if ($thread->locked) {{ 'checked' }} @endif>
                        </div>
                    </div>

                    <input type="button" id="editthreadsubmit" onclick="document.getElementById('formEditForumThread').submit();" class="is-hidden">
                </form>
            </section>
            <footer class="modal-card-foot is-stretched">
                <button class="button is-success" onclick="document.getElementById('editthreadsubmit').click();">{{ __('app.save') }}</button>
                <button class="button" onclick="vue.bShowEditForumThread = false;">{{ __('app.cancel') }}</button>
            </footer>
        </div>
    </div>

    <div class="modal" :class="{'is-active': bShowEditForumPost}">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head is-stretched">
                <p class="modal-card-title">{{ __('app.edit_thread') }}</p>
                <button class="delete" aria-label="close" onclick="vue.bShowEditForumPost = false;"></button>
            </header>
            <section class="modal-card-body is-stretched">
                <form id="formEditForumPost" method="POST" action="{{ url('/forum/thread/post/edit') }}">
                    @csrf

                    <input type="hidden" id="forum-post-id" name="id">

                    <div class="field">
                        <label class="label">{{ __('app.message') }}</label>
                        <div class="control">
                            <textarea name="message" id="forum-edit-thread-post-post"></textarea>
                        </div>
                    </div>

                    <input type="button" id="editforumpostsubmit" onclick="document.getElementById('formEditForumPost').submit();" class="is-hidden">
                </form>
            </section>
            <footer class="modal-card-foot is-stretched">
                <button class="button is-success" onclick="document.getElementById('editforumpostsubmit').click();">{{ __('app.save') }}</button>
                <button class="button" onclick="vue.bShowEditForumPost = false;">{{ __('app.cancel') }}</button>
            </footer>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        window.paginate = null;

        window.listPostings = function() {
                if (window.paginate === null) {
                    document.getElementById('postings').innerHTML = '<div id="spinner"><center><i class="fas fa-spinner fa-spin"></i></center></div>';
                } else {
                    document.getElementById('postings').innerHTML += '<div id="spinner"><center><i class="fas fa-spinner fa-spin"></i></center></div>';
                }

                if (document.getElementById('loadmore')) {
                    document.getElementById('loadmore').remove();
                }

                window.vue.ajaxRequest('post', '{{ url('/forum/thread/' . $thread->id . '/posts') }}', { paginate: window.paginate }, function(response){
                    if (response.code == 200) {
                        if (document.getElementById('spinner')) {
                            document.getElementById('spinner').remove();
                        }

                        if (response.data.length > 0) {
                            response.data.forEach(function(elem, index) {
                                let html = window.vue.renderForumPostingItem(elem, {{ (($user) && ($user->admin || $user->maintainer)) ? 'true' : 'false' }}, ({{ (($user) ? $user->id : -1) }} == elem.userId));

                                document.getElementById('postings').innerHTML += html;
                            });

                            window.paginate = response.data[response.data.length - 1].id;

                            document.getElementById('postings').innerHTML += '<div id="loadmore"><br/><center><a href="javascript:void(0);" onclick="window.listPostings();">{{ __('app.load_more') }}</a></center></div>';
                        } else {
                            document.getElementById('postings').innerHTML += '{{ __('app.forums_no_posts_found') }}';
                        }
                    }
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            window.listPostings();
        });
    </script>
@endsection
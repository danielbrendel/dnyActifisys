{{--
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2022 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

@extends('layouts.layout_home')

@section('title', env('APP_PROJECTNAME') . ' - ' . $post->thread->title . ' - ' . __('app.single_post'))

@section('content')
    <div class="column is-2"></div>

    <div class="column is-8 fixed-form" id="feed-left">
        <div class="is-default-padding">
            <h1 class="is-pointer" onclick="location.href = '{{ url('/forum/thread/' . $post->thread->id . '/show') }}';">{{ $post->thread->title }} @if ($post->thread->sticky) <i class="fas fa-thumbtack"></i> @endif @if ($post->thread->locked) <i class="fas fa-lock"></i> @endif</h1>

            <div>
                <div class="is-inline-block is-avatar-icon"><a href="{{ url('/user/' . $post->user->id) }}"><img src="{{ asset('gfx/avatars/' . $post->user->avatar) }}" alt="avatar"/></a></div>
                <div class="is-inline-block"><a href="{{ url('/user/' . $post->user->id) }}">{{ $post->user->username }}</a></div>
            </div>
        </div>

        <div id="postings">
            <div class="forum-posting">
                <div class="forum-posting-userinfo">
                    <div class="forum-posting-userinfo-avatar"><a href="{{ url('/user/' . $post->user->id) }}"><img src="{{ asset('gfx/avatars/' . $post->user->avatar) }}" alt="avatar"/></a></div>
                    <div class="forum-posting-userinfo-name"><a href="{{ url('/user/' . $post->user->id) }}">{{ $post->user->username }}</a></div>
                </div>

                <div class="forum-posting-message">
                    <div class="forum-posting-message-content is-breakall">
                        @if ($post->locked) <i class="is-color-grey"> @endif <div id="forum-thread-post-message-{{ $post->id }}">{{ $post->message }}</div> @if ($post->locked) </i> @endif
                        @if ((!$post->locked) && ($post->created_at !== $post->updated_at)) <br/><br/><i class="is-color-grey is-font-small">{{ __('app.forum_post_edited_info') }} {{ $post->updated_at->diffForHumans() }}</i> @endif
                    </div>

                    <div class="forum-posting-message-footer">
                        <span class="is-color-grey" title="{{ $post->created_at }}">{{ $post->created_at->diffForHumans() }}</span> | <a href="javascript:void(0);" onclick="window.reportForumPost({{ $post->id }})">{{ __('app.report') }}</a>
                        
                        @if (($user->admin) || ($user->maintainer))
                        | <a href="javascript:void(0);" onclick="window.lockForumPost({{ $post->id }});">{{ __('app.lock') }}</a>
                        @endif

                        @if (($post->userId == $user->id) || (($user->admin) || ($user->maintainer)))
                        | <a href="javascript:void(0);" onclick="window.quillEditorPostEdit.setContents(window.quillEditorPostEdit.clipboard.convert(document.getElementById('forum-thread-post-message-{{ $post->id }}').innerHTML)); window.vue.bShowEditForumPost = true;">{{ __('app.edit') }}</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="column is-2"></div>
@endsection

@section('modal')
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

                    <input type="hidden" name="id" value="{{ $post->id }}">

                    <div class="field">
                        <label class="label">{{ __('app.message') }}</label>
                        <div class="control">
                            <div id="input-forum-post-single">{{ $post->message }}</div>
                            <textarea class="is-hidden" id="edit-forum-post-single-post" name="message"></textarea>
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

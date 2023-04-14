{{--
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2023 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

@extends('layouts.layout_home')

@section('title', env('APP_PROJECTNAME') . ' - ' . __('app.gallery'))

@section('content')
    <div class="column is-4"></div>

    <div class="column is-4 is-no-padding">
        <div id="gallery">
            <div class="gallery-item">
                <div class="gallery-item-image is-pointer" style="background-image: url('{{ asset('/gfx/gallery/' . $item->image_thumb) }}');" onclick="window.open('{{ asset('/gfx/gallery/' . $item->image_full) }}');"></div>

                <div class="gallery-item-info">
                    <div class="gallery-item-info-title">
                        {{ $item->title }}
                        
                        <div class="gallery-item-dropdown">
                            <div class="dropdown is-right" id="gallery-item-dropdown-{{ $item->id }}">
                                <div class="dropdown-trigger">
                                    <i class="fas fa-ellipsis-v is-pointer" onclick="window.vue.toggleContextMenu(document.getElementById('gallery-item-dropdown-{{ $item->id }}'));"></i>
                                </div>
                                <div class="dropdown-menu" role="menu">
                                    <div class="dropdown-content">
                                        <a onclick="window.vue.toggleContextMenu(document.getElementById('gallery-item-dropdown-{{ $item->id }}'));" href="whatsapp://send?text={{ url('/gallery/item/' . $item->slug) }} - {{ $item->title }}" class="dropdown-item">
                                            <i class="fab fa-whatsapp"></i>&nbsp;{{ __('app.share_whatsapp') }}
                                        </a>
                                        <a onclick="window.vue.toggleContextMenu(document.getElementById('gallery-item-dropdown-{{ $item->id }}'));" href="https://twitter.com/share?url={{ urlencode(url('/gallery/item/' . $item->slug)) }}&text={{ $item->title }}" class="dropdown-item">
                                            <i class="fab fa-twitter"></i>&nbsp;{{ __('app.share_twitter') }}
                                        </a>
                                        <a onclick="window.vue.toggleContextMenu(document.getElementById('gallery-item-dropdown-{{ $item->id }}'));" href="https://www.facebook.com/sharer/sharer.php?u={{ url('/gallery/item/' . $item->slug) }}" class="dropdown-item">
                                            <i class="fab fa-facebook"></i>&nbsp;{{ __('app.share_facebook') }}
                                        </a>
                                        <a onclick="window.vue.toggleContextMenu(document.getElementById('gallery-item-dropdown-{{ $item->id }}'));" href="mailto:name@domain.com?body={{ url('/gallery/item/' . $item->slug) }} - {{ $item->title }}" class="dropdown-item">
                                            <i class="far fa-envelope"></i>&nbsp;{{ __('app.share_email') }}
                                        </a>
                                        <a onclick="window.vue.toggleContextMenu(document.getElementById('gallery-item-dropdown-{{ $item->id }}'));" href="sms:000000000?body={{ url('/gallery/item/' . $item->slug) }} - {{ $item->title }}" class="dropdown-item">
                                            <i class="fas fa-sms"></i>&nbsp;{{ __('app.share_sms') }}
                                        </a>
                                        <a href="javascript:void(0)" onclick="window.vue.copyToClipboard('{{ url('/gallery/item/' . $item->slug) }} - {{ $item->title }}'); window.vue.toggleContextMenu(document.getElementById('gallery-item-dropdown-{{ $item->id }}'));" class="dropdown-item">
                                            <i class="far fa-copy"></i>&nbsp;{{ __('app.share_clipboard') }}
                                        </a>

                                        @auth
                                            <hr class="dropdown-divider">

                                            @if (auth()->id() !== $item->userId)
                                            <a class="dropdown-item is-color-black" href="{{ url('/gallery/' . $item->id . '/report') }}">
                                                {{ __('app.report') }}
                                            </a>
                                            @endif

                                            @if ((auth()->id() == $item->userId) || (($user->admin) || ($user->maintainer)))
                                            <a class="dropdown-item is-color-black" href="{{ url('/gallery/' . $item->id . '/remove') }}">
                                                {{ __('app.remove') }}
                                            </a>
                                            @endif
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="gallery-item-info-location"><i class="fas fa-map-marker-alt is-color-dark-grey"></i> {{ $item->location }}</div>
                
                    <div class="gallery-item-info-tags">
                        @foreach ($item->tags as $tag)
                            @if (strlen($tag) > 0)
                                <div class="gallery-item-info-tag">
                                    <a href="{{ url('/gallery?tag=' . $tag) }}">#{{ $tag }}</a>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <div class="gallery-item-footer">
                    <div class="gallery-item-footer-inner">
                        <div class="gallery-item-footer-user"><a href="{{ url('/user/' . $item->user->slug) }}">{{ __('app.gallery_item_by', ['name' => $item->user->name]) }}</a></div>
                        
                        <div class="gallery-item-footer-stats">
                            <div class="gallery-item-footer-comments">
                                <span>{{ $item->comment_count }}</span>
                                <span><i class="far fa-comments"></i></span>
                            </div>
                            <div class="gallery-item-footer-likes">
                                <span id="count-like-{{ $item->id }}">{{ $item->likes }}</span>&nbsp;
                                <span><a href="javascript:void(0);" onclick="window.vue.toggleLike({{ $item->id }}, 'action-like-{{ $item->id }}', 'count-like-{{ $item->id }}');"><i class="@if ($item->hasLiked) fas @else far @endif fa-heart" id="action-like-{{ $item->id }}"></i></a></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="gallery-thread-input-avatar is-inline-block">
                    <img src="{{ asset('gfx/avatars/' . ((auth()->id() != null) ? \App\User::get(auth()->id())->avatar : 'default.png' )) }}">
                </div>

                <div class="gallery-thread-input-form is-inline-block is-def-color">
                    <form method="POST" action="{{ url('/gallery/thread/add') }}">
                        @csrf

                        <div class="gallery-thread-input-form-text">
                            <textarea name="message" placeholder="{{ __('app.type_a_message') }}"></textarea>
                        </div>

                        <input type="hidden" name="item" value="{{ $item->id }}"/>

                        <div class="gallery-thread-input-form-button">
                            @auth
                                <input type="submit" class="button is-link" value="{{ __('app.send') }}"/>
                            @elseguest
                                <button type="button" class="button is-link" onclick="window.vue.bShowLogin = true;">{{ __('app.send') }}</button> 
                            @endauth
                        </div>
                    </form>
                </div>
            </div>

            <div id="gallery-thread">
                <div class="gallery-thread-item">
                    
                </div>
            </div>
        </div>
    </div>

    <div class="column is-4"></div>
@endsection

@section('javascript')
<script>
        window.paginate = null;

        @auth
            window.isAuth = true;
        @elseguest
            window.isAuth = false;
        @endauth

        window.fetchGalleryItemThread = function() {
                if (window.paginate === null) {
                    document.getElementById('gallery-thread').innerHTML = '<div id="spinner"><center><i class="fas fa-spinner fa-spin"></i></center></div>';
                } else {
                    document.getElementById('gallery-thread').innerHTML += '<div id="spinner"><center><i class="fas fa-spinner fa-spin"></i></center></div>';
                }

                if (document.getElementById('loadmore')) {
                    document.getElementById('loadmore').remove();
                }

                window.vue.ajaxRequest('post', '{{ url('/gallery/' . $item->id . '/thread/fetch') }}', { paginate: window.paginate }, function(response){
                    if (response.code == 200) {
                        if (document.getElementById('spinner')) {
                            document.getElementById('spinner').remove();
                        }

                        if (response.data.length > 0) {
                            response.data.forEach(function(elem, index) {
                                let html = window.vue.renderGalleryThreadItem(elem, elem.adminOrOwner, window.isAuth);

                                document.getElementById('gallery-thread').innerHTML += html;
                            });

                            window.paginate = response.data[response.data.length - 1].id;

                            document.getElementById('gallery-thread').innerHTML += '<div id="loadmore"><center><a class="is-def-color" href="javascript:void(0);" onclick="window.fetchGalleryItemThread();">{{ __('app.load_more') }}</a></center></div>';
                        } else {
                            document.getElementById('gallery-thread').innerHTML += '<div class="is-def-color"><center><br/>{{ __('app.gallery_no_items_found') }}</center></div>';
                        }
                    }
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            window.fetchGalleryItemThread();
        });
    </script>
@endsection


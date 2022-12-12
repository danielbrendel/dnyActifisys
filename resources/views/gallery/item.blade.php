{{--
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2022 by Daniel Brendel

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
                        <div class="gallery-item-footer-likes">
                            <span id="count-like-{{ $item->id }}">{{ $item->likes }}</span>&nbsp;
                            <span><a href="javascript:void(0);" onclick="window.vue.toggleLike({{ $item->id }}, 'action-like-{{ $item->id }}', 'count-like-{{ $item->id }}');"><i class="@if ($item->hasLiked) fas @else far @endif fa-heart" id="action-like-{{ $item->id }}"></i></a></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="column is-4"></div>
@endsection


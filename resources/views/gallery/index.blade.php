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
    <div class="column is-2"></div>

    <div class="column is-8 is-no-padding">
        <div class="is-side-padding">
            <h3>{{ __('app.gallery_subtitle') }}</h2>

            <div class="gallery-text">{!! \App\AppModel::getGalleryText() !!}</div>
        </div>

        <div class="is-side-padding">
            <div class="gallery-submit is-inline-block">
                <a class="button is-success" href="javascript:void(0);" onclick="@auth window.vue.bShowGalleryUpload = true; @elseguest window.vue.bShowLogin = true; @endauth">{{ __('app.gallery_submit') }}</a>
            </div>

            @if (isset($_GET['tag']))
            <div class="is-inline-block">
                <a class="is-color-grey" href="{{ url('/gallery') }}">{{ __('app.clear_tag', ['tag' => $_GET['tag']]) }}</a>
            </div>
            @endif
        </div>

        <div class="field">
            <hr/>
        </div>

        <div id="gallery"></div>
    </div>

    <div class="column is-2"></div>
@endsection

@section('modal')
    <div class="modal" :class="{'is-active': bShowGalleryUpload}">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head is-stretched">
                <p class="modal-card-title">{{ __('app.gallery_upload') }}</p>
                <button class="delete" aria-label="close" onclick="vue.bShowGalleryUpload = false;"></button>
            </header>
            <section class="modal-card-body is-stretched">
                <form id="frmAddGalleryItem" method="POST" action="{{ url('/gallery/add') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="field">
                        <div class="control">
                            <input type="file" name="image" data-role="file" data-type="2" required/>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label">{{ __('app.title') }}</label>
                        <div class="control">
                            <input type="text" name="title" required/>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label">{{ __('app.location') }}</label>
                        <div class="control">
                            <input type="text" name="location" required/>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label">{{ __('app.tags') }}</label>
                        <div class="control">
                            <input type="text" name="tags" placeholder="{{ __('app.tags_example_placeholder') }}"/>
                        </div>
                    </div>
                </form>
            </section>
            <footer class="modal-card-foot is-stretched">
                <button class="button is-success" onclick="document.getElementById('frmAddGalleryItem').submit();">{{ __('app.submit') }}</button>
                <button class="button" onclick="vue.bShowGalleryUpload = false;">{{ __('app.cancel') }}</button>
            </footer>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        window.paginate = null;

        @if (isset($_GET['tag']))
        window.filterByTag = '{{ $_GET['tag'] }}';
        @else
        window.filterByTag = null;
        @endif

        window.fetchGalleryItems = function() {
                if (window.paginate === null) {
                    document.getElementById('gallery').innerHTML = '<div id="spinner"><center><i class="fas fa-spinner fa-spin"></i></center></div>';
                } else {
                    document.getElementById('gallery').innerHTML += '<div id="spinner"><center><i class="fas fa-spinner fa-spin"></i></center></div>';
                }

                if (document.getElementById('loadmore')) {
                    document.getElementById('loadmore').remove();
                }

                window.vue.ajaxRequest('post', '{{ url('/gallery/fetch') }}', { paginate: window.paginate, tag: window.filterByTag }, function(response){
                    if (response.code == 200) {
                        if (document.getElementById('spinner')) {
                            document.getElementById('spinner').remove();
                        }

                        if (response.data.length > 0) {
                            response.data.forEach(function(elem, index) {
                                let html = window.vue.renderGalleryItem(elem);

                                document.getElementById('gallery').innerHTML += html;
                            });

                            window.paginate = response.data[response.data.length - 1].id;

                            if (!response.last) {
                                document.getElementById('gallery').innerHTML += '<div id="loadmore"><center><a class="is-def-color" href="javascript:void(0);" onclick="window.fetchGalleryItems();">{{ __('app.load_more') }}</a></center></div>';
                            }
                        } else {
                            document.getElementById('gallery').innerHTML += '<div class="is-def-color"><center><br/>{{ __('app.gallery_no_items_found') }}</center></div>';
                        }
                    }
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            window.fetchGalleryItems();
        });
    </script>
@endsection
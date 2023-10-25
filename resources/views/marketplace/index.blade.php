{{--
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2023 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

@extends('layouts.layout_home')

@section('title', __('app.marketplace'))

@section('content')
    <div class="column is-2"></div>

    <div class="column is-8 is-no-padding">
        <div class="is-side-padding">
            <h3>{{ __('app.marketplace_subtitle') }}</h2>

            <div class="marketplace-text">{!! \App\AppModel::getMarketplaceText() !!}</div>
        </div>

        <div class="field has-addons fixed-form is-side-padding">
            <div class="control">
                <select onchange="window.marketCategory = this.value; window.paginate = null; window.listAdverts();">
                    <option value="0">{{ __('app.category_all') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="control">
                <label class="label">&nbsp;&nbsp;&nbsp;{{ __('app.choose_market_category') }}</label>
            </div>
        </div>

        <div class="field">
            <hr/>
        </div>

        <div id="marketadverts"></div>
    </div>

    <div class="column is-2"></div>
@endsection

@section('javascript')
    <script>
        window.paginate = null;
        window.marketCategory = 0;

        window.listAdverts = function() {
                if (window.paginate === null) {
                    document.getElementById('marketadverts').innerHTML = '<div id="spinner"><center><i class="fas fa-spinner fa-spin"></i></center></div>';
                } else {
                    document.getElementById('marketadverts').innerHTML += '<div id="spinner"><center><i class="fas fa-spinner fa-spin"></i></center></div>';
                }

                if (document.getElementById('loadmore')) {
                    document.getElementById('loadmore').remove();
                }

                window.vue.ajaxRequest('post', '{{ url('/marketplace/list') }}', { paginate: window.paginate, category: window.marketCategory }, function(response){
                    if (response.code == 200) {
                        if (document.getElementById('spinner')) {
                            document.getElementById('spinner').remove();
                        }

                        if (response.data.length > 0) {
                            response.data.forEach(function(elem, index) {
                                let html = window.vue.renderMarketItem(elem);

                                document.getElementById('marketadverts').innerHTML += html;
                            });

                            window.paginate = response.data[response.data.length - 1].id;

                            if (!response.last) {
                                document.getElementById('marketadverts').innerHTML += '<div id="loadmore"><center><a class="is-def-color" href="javascript:void(0);" onclick="window.listAdverts();">{{ __('app.load_more') }}</a></center></div>';
                            }
                        } else {
                            document.getElementById('marketadverts').innerHTML += '<div class="is-def-color"><br/>{{ __('app.marketplace_no_adverts_found') }}</div>';
                        }
                    }
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            window.listAdverts();
        });
    </script>
@endsection
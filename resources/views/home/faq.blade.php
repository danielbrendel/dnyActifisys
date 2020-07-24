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
    <div class="column is-4"></div>

    <div class="column is-4">
        <div>
            <h1>{{ __('app.faq') }}</h1>

            <div data-role="accordion" data-one-frame="true" data-show-active="true">
                @foreach ($faqs as $faq)
                    <div class="frame">
                        <div class="heading">{{ $faq->question }}</div>
                        <div class="content">
                            <div class="p-2">{{ $faq->answer }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="column is-4"></div>
@endsection

{{--
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2023 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

@extends('layouts.layout_home')

@section('title')
    {{ __('app.maintainer_area') }}
@endsection

@section('content')
    <div class="column is-2"></div>

    <div class="column is-8">
        <div class="fixed-form is-default-padding">
            <div>
                <h1>{{ __('app.visits') }}</h1>
            </div>

            <div class="paragraph">
                {{ __('app.currently_online') }} <span id="stats-online-count">{{ $online_count }}</span>
            </div>

            <div class="visitor-summary">
                <div>
                    {{ __('app.range') }} <input class="stats-input" type="date" id="inp-date-from"/>&nbsp;<input class="stats-input" type="date" id="inp-date-till"/>&nbsp;
                    <select class="stats-input" onchange="window.vue.renderStats('visitor-stats', this.value, '{{ $end }}');">
                        <option value="{{ $start }}">{{ __('app.select_range') }}</option>
                        @foreach ($predefined_dates as $key => $value)
                            <option value="{{ $value }}">{{ $key }}</option>
                        @endforeach
                    </select>
                    &nbsp;<a class="button" href="javascript:void(0);" onclick="window.vue.renderStats('visitor-stats', document.getElementById('inp-date-from').value, document.getElementById('inp-date-till').value);">Go</a>
                </div>
                
                <div>{{ __('app.sum') }} <div class="is-inline-block" id="count-total"></div></div>
                <div>{{ __('app.avg_per_day') }} <div class="is-inline-block" id="count-avg-day"></div></div>
                <div>{{ __('app.avg_per_hour') }} <div class="is-inline-block" id="count-avg-hour"></div></div>
            </div>

            <div class="page-content">
                <canvas id="visitor-stats"></canvas>
            </div>
        </div>
    </div>

    <div class="column is-2"></div>
@endsection

@section('javascript')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.statsChart = null;

            window.vue.renderStats('visitor-stats', '{{ $start }}', '{{ $end }}');
        });
    </script>
@endsection

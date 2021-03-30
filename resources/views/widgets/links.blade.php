{{--
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2021 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

<div class="links">
    <div class="columns">
        <div class="column is-3"></div>

        <div class="column is-6">
            <center>
                <div class="linklist">
                    @if (env('TWITTER_NEWS', null) !== null)
                        <div class="linkitem"><a href="{{ url('/news') }}">{{ __('app.news') }}</a></div>
                    @endif
                    <div class="linkitem"><a href="{{ url('/tos') }}">{{ __('app.tos') }}</a></div>
                    <div class="linkitem"><a href="{{ url('/faq') }}">{{ __('app.faq') }}</a></div>
                    <div class="linkitem"><a href="{{ url('/imprint') }}">{{ __('app.imprint') }}</a></div>
                    @if (env('HELPREALM_WORKSPACE', null) !== null)
                        <div class="linkitem"><a href="{{ url('/contact') }}">{{ __('app.contact') }}</a></div>
                    @endif
                </div>
            </center>
        </div>

        <div class="column is-3"></div>
    </div>
</div>

{{--
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2023 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

<head>
    @if (env('GA_TOKEN'))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ env('GA_TOKEN') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ env('GA_TOKEN') }}');
        </script>
    @endif

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ env('APP_PROJECTNAME') }} - @yield('title')</title>

    <meta name="author" content="{{ env('APP_AUTHOR') }}">
    <meta name="description" content="{{ env('APP_DESCRIPTION') }}">
    <meta name="keywords" content="{{ env('APP_TAGS') }}">

    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('css/bulma.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/metro-all.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">

    @if (env('APP_ENV') == 'local')
    <script src="{{ asset('js/vue.js') }}"></script>
    @elseif (env('APP_ENV') == 'production')
    <script src="{{ asset('js/vue.min.js') }}"></script>
    @endif
    <script src="{{ asset('js/fontawesome.js') }}"></script>
    <script src="{{ asset('js/metro.min.js') }}"></script>
    @if (env('STRIPE_ENABLE'))
    <script src="https://js.stripe.com/v3/"></script>
    @endif

    {!! \App\AppModel::getHeadCode() !!}
</head>
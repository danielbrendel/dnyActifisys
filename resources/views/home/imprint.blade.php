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
    {{ __('app.imprint') }}
@endsection

@section('content')
    <div class="column is-4"></div>

    <div class="column is-4">
        <div class="is-color-grey">
            {!! $imprint_content !!}
        </div>
    </div>

    <div class="column is-4"></div>
@endsection

{{--
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2021 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

@if (strlen($about_content) > 0)
<div class="about">
    <div class="columns">
        <div class="column is-3"></div>

        <div class="column is-6">
            {!! $about_content !!}
        </div>

        <div class="column is-3"></div>
    </div>
</div>
@endif

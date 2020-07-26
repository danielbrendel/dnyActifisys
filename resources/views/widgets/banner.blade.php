{{--
    ComAct (dnyComAct) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

<div class="banner" style="background-image: url('{{ asset('gfx/' . \App\AppModel::getHomeBanner()) }}')">
    <div class="columns">
        <div class="column is-6"></div>

        <div class="column-is-6">
            <div class="banner-headline is-default-padding">
                <h1>{{ \App\AppModel::getHeadlineTop() }}</h1>

                <h3>{{ \App\AppModel::getHeadlineSub() }}</h3>
            </div>
        </div>
    </div>
</div>

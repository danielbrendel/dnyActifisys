{{--
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2023 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

@if (isset($announcements))
    @foreach ($announcements as $key => $announcement)
        <div id="announcement-message-{{ $key }}" class="is-z-index-3">
            <article class="message is-info">
                <div class="message-header">
                    <p>{{ $announcement->title }}</p>
                    <button class="delete" aria-label="delete" onclick="document.getElementById('announcement-message-{{ $key }}').style.display = 'none';"></button>
                </div>
                <div class="message-body">
                    {!! $announcement->content !!}
                </div>
            </article>
        </div>
    @endforeach
@endif
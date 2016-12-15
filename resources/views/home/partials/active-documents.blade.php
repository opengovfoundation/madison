<section class="active-documents">
    <h2 class="heading">{{ trans('messages.activelegislation') }}</h2>

    <div class="recent-activity">
        <h3>{{ trans('messages.recentactivity') }}</h3>

        @foreach ($mostRecentDocuments as $recentDocument)
            <article>
                <a href="/documents/{{ $recentDocument['slug'] }}"
                    class="document-title" title="{{ $recentDocument['title'] }}">
                    {{ $recentDocument['title'] }}
                </a>
                <span class="date">
                    {{ trans('messages.updated') }}
                    {{ $recentDocument->updated_at->diffForHumans() }}
                </span>
            </article>
        @endforeach
    </div>

    <div class="move-active">
        <h3>{{ trans('messages.mostactive') }}</h3>

        @foreach ($mostActiveDocuments as $activeDocument)
            <article>
                <a href="/documents/{{ $activeDocument['slug'] }}"
                    class="document-title" title="{{ $activeDocument['title'] }}">
                    {{ $activeDocument['title'] }}
                </a>
                <div class="document-stats">
                    <ul>
                        <li>{{ $activeDocument['comment_count'] }} {{ trans('messages.document.comments') }}</li>
                        <li>{{ $activeDocument['note_count'] }} {{ trans('messages.document.notes') }}</li>
                    </ul>
                </div>
            </article>
        @endforeach
    </div>
</section>

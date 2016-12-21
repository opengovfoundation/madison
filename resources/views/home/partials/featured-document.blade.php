<article class="main-feature">
    <header class="document-info">
        <div class="document-thumbnail-container">
            <img src="{{ $document['featuredImageUrl'] }}" alt="" class="document-thumbnail">
        </div>
        <h2 class="heading">
            <a href="/documents/{{ $document['slug'] }}" title="{{ $document['title'] }}" rel="bookmark">
                {{ $document['title'] }}
            </a>
        </h2>

        <div class="document-sponsors">
            {{ trans('messages.document.sponsoredby') }}
            <ul>
                @foreach ($document['sponsors'] as $sponsor)
                    {{-- TODO - Would be cool to link this to a sponsor view page --}}
                    <li class="document-sponsor">{{ $sponsor['display_name'] }}</li>
                @endforeach
            </ul>
        </div>
        <div class="document-stats">
            <ul>
                <li>{{ $document['comment_count'] }} {{ trans('messages.document.comments') }}</li>
                <li>{{ $document['note_count'] }} {{ trans('messages.document.notes') }}</li>
                <li>{{ trans('messages.updated') }} {{ date('M d, Y', strtotime($document['updated_at'])) }}</li>
            </ul>
        </div>
    </header>
    <div class="document-content">
        <div class="document-summary">
            {!! GrahamCampbell\Markdown\Facades\Markdown::convertToHtml($document['introtext']) !!}
        </div>

        <a class="read-more-button" href="/documents/{{ $document['slug'] }}" rel="bookmark" role="button"
            title="{{ $document['title'] }}">{{ trans('messages.readmore') }}</a>
    </div>
</article>

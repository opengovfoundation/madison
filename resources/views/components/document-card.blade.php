<div class="document-card">
    <h3>
        <a href="{{ route('documents.show', $document) }}">
            {{ $document->title }}
        </a>
        <br>
        <small class="text-muted">
            @lang('messages.document.sponsoredby', ['sponsors' => $document->sponsors->implode('display_name', ', ')])
        </small>
    </h3>

    @if (isset($showIntro) && $showIntro)
        <p>{{ $document->shortIntroText() }}</p>
    @endif

    <p class="document-info">
        <small>@include('components/relative-time', ['datetime' => $document->created_at])</small>
        <small>
            <i class="fa fa-thumbs-up"></i>
            {{ $document->support }}
        </small>
        <small>
            <i class="fa fa-thumbs-down"></i>
            {{ $document->oppose }}
        </small>
        <small>
            <i class="fa fa-comments"></i>
            {{ $document->all_comments_count }}
        </small>
    </p>
</div>

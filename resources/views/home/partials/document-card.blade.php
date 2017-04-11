<div class="col-md-4">
    <div class="thumbnail doc-card">
        <a href="{{ route('documents.show', $document) }}">
            <img src="{{ $document->getFeaturedImageUrl() }}" class="img-responsive">
        </a>
        <div class="caption">
            <div class="intro">
                <h4>
                    <a href="{{ route('documents.show', $document) }}">
                        {{ $document->title }}
                    </a>
                    <br>
                    <small class="text-muted">
                        {{ $document->sponsors->implode('display_name', ', ') }}
                    </small>
                </h4>
                <p>{{ $document->shortIntroText() }}</p>
            </div>

            <hr>

            <div class="row">
                <div class="col-xs-6">
                    <small class="text-muted">{{ $document->created_at->toDateString() }}</small>
                </div>
                <div class="col-xs-6 text-right">
                    <small class="text-muted">{{ $document->all_comments_count }} {{ trans('messages.document.comments') }}</small>
                </div>
            </div>
        </div>
    </div>
</div>

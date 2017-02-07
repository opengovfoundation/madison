<div class="comment" id="{{ $comment->str_id }}">
    <h4>
        <strong>{{ $comment->user->display_name }}</strong>
        <span class="small">{{ $comment->created_at->diffForHumans() }}</span>
    </h4>

    <p>{{ $comment->annotationType->content }}</p>

    <div class="row">
        <div class="col-md-12">
            <div class="activity-actions pull-left">
                <a class="thumbs-up" onclick="$(this).trigger('madison.addAction')"
                    data-action-type="likes" data-annotation-id="{{ $comment->str_id }}"
                    title="{{ trans('messages.document.like') }}"
                    aria-label="{{ trans('messages.document.like') }}" role="button">

                    <span class="action-count">{{ $comment->likes_count }}</span>
                </a>

                <a class="flag" onclick="$(this).trigger('madison.addAction')"
                    data-action-type="flags" data-annotation-id="{{ $comment->str_id }}"
                    title="{{ trans('messages.document.flag') }}"
                    aria-label="{{ trans('messages.document.flag') }}" role="button">

                    <span class="action-count">{{ $comment->flags_count }}</span>
                </a>

                <a class="link" href="{{ $comment->getLink() }}"
                    aria-label="{{ trans('messages.permalink') }}" role="button"
                    title="{{ trans('messages.permalink') }}">&nbsp;</a>

                @if ($comment->annotatable_type === \App\Models\Doc::ANNOTATABLE_TYPE)
                    <a class="comments" aria-label="{{ trans('messages.document.replies') }}
                        title="{{ trans('messages.document.replies') }} role="button"
                        data-comment-id="{{ $comment->str_id }}">

                        <span class="action-count">{{ $comment->comments()->count() }}</span>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="row comment-replies">
        @if (Auth::user() && $comment->annotatable_type === \App\Models\Doc::ANNOTATABLE_TYPE)
            <div class="col-md-12">
                <hr>
                {{ Form::open(['route' => ['documents.comments.storeReply', $comment->annotatable_id, $comment->id]]) }}
                    {{ Form::mInput(
                        'textarea',
                        'text',
                        trans('messages.document.add_reply'),
                        null,
                        [ 'rows' => 3 ]
                    ) }}
                    {{ Form::mSubmit() }}
                {{ Form::close() }}
            </div>
        @endif
        @if ($comment->comments()->count() > 0)
            <div class="col-md-12">
                <hr>
                @each ('documents/partials/comment', $comment->comments()->get(), 'comment')
            </div>
        @endif
    </div>

    <hr>
</div>

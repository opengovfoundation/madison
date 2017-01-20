<div class="comment" id="comment_{{ $comment->id }}">
    <h4>
        <strong>{{ $comment->user->display_name }}</strong>
        <span class="small">{{ $comment->created_at->diffForHumans() }}</span>
    </h4>

    <p>{{ $comment->annotationType->content }}</p>

    <div class="row">
        <div class="col-md-12">
            <div class="activity-actions pull-left">
                <a class="thumbs-up" onclick="$(this).trigger('madison.addAction')"
                    data-action-type="likes" data-annotation-id="{{ $comment->id }}"
                    title="{{ trans('messages.document.like') }}"
                    aria-label="{{ trans('messages.document.like') }}" role="button">

                    <span class="action-count">{{ $comment->likes_count }}</span>
                </a>

                <a class="flag" onclick="$(this).trigger('madison.addAction')"
                    data-action-type="flags" data-annotation-id="{{ $comment->id }}"
                    title="{{ trans('messages.document.flag') }}"
                    aria-label="{{ trans('messages.document.flag') }}" role="button">

                    <span class="action-count">{{ $comment->flags_count }}</span>
                </a>

                <a class="link" href="{{ $comment->getLink() }}"
                    aria-label="{{ trans('messages.permalink') }}" role="button"
                    title="{{ trans('messages.permalink') }}">&nbsp;</a>

                @if ($comment->annotatable_type === \App\Models\Doc::ANNOTATABLE_TYPE)
                    @if ($comment->comments()->count() > 0)
                        <a class="comments" aria-label="{{ trans('messages.document.replies') }}
                            title="{{ trans('messages.document.replies') }} role="button"
                            data-comment-id="{{ $comment->id }}">

                            <span class="action-count">{{ $comment->comments()->count() }}</span>
                        </a>
                    @else
                        <span class="comments" aria-label="{{ trans('messages.document.replies') }}
                            title="{{ trans('messages.document.replies') }}>

                            <span class="action-count">{{ $comment->comments()->count() }}</span>
                        </span>
                    @endif
                @endif
            </div>
        </div>
    </div>

    @if ($comment->comments()->count() > 0)
        <div class="comment-replies hide">
            <hr>
            @each ('documents/partials/comment', $comment->comments()->get(), 'comment')
        </div>
    @endif
    <hr>
</div>

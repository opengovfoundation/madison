<div class="media-left">
    <img class="media-object" alt="{{ trans('messages.user.avatar_alt_text') }}" src="{{ $comment->user->avatar }}">
</div>

<div class="media-body">
    <div class="comment-content">
        <h4 class="media-heading">
            {{ $comment->user->display_name }}
            <br>

            <a href="{{ $comment->getLink() }}"
                aria-label="{{ trans('messages.permalink') }}" role="button"
                title="{{ trans('messages.permalink') }}">
            <small>@include('components/relative-time', [ 'datetime' => $comment->created_at ])</small>
                </a>

            <a class="btn btn-simple like-btn" onclick="$(this).trigger('madison.addAction')"
                data-action-type="likes" data-annotation-id="{{ $comment->str_id }}"
                title="{{ trans('messages.document.like') }}"
                aria-label="{{ trans('messages.document.like') }}" role="button">

                <i class="fa fa-thumbs-o-up" aria-hidden="true"></i>
                <span class="action-count">{{ $comment->likes_count }}</span>
            </a>
        </h4>

        @if (!empty($comment->data['quote']))
            <a onclick="anchorToHighlight('{{ $comment->str_id }}')">
                <blockquote>
                    <p>{{ $comment->data['quote'] }}</p>
                </blockquote>
            </a>
        @endif

        <p>{{ $comment->annotationType->content }}</p>

        <div class="bottom">
            @if ($comment->annotatable_type === \App\Models\Doc::ANNOTATABLE_TYPE && $comment->comments()->count() > 0)
                <div class="comment-replies-toggle pull-left">
                    <a class="comment-replies-toggle-show" aria-label="{{ trans('messages.document.replies') }}
                        title="{{ trans('messages.document.replies') }} role="button"
                        data-comment-id="{{ $comment->str_id }}">

                        @lang('messages.document.see_replies', ['count' => $comment->comments()->count()])
                    </a>
                </div>
            @endif

            <a class="btn btn-simple pull-right" onclick="$(this).trigger('madison.addAction')"
                data-action-type="flags" data-annotation-id="{{ $comment->str_id }}"
                title="{{ trans('messages.document.flag') }}"
                aria-label="{{ trans('messages.document.flag') }}" role="button">

                <i class="fa fa-flag" aria-hidden="true"></i>
                <span class="action-count">{{ $comment->flags_count }}</span>
            </a>
        </div>
    </div>

    @if (Auth::user() && $comment->annotatable_type === \App\Models\Doc::ANNOTATABLE_TYPE)
        <div class="clearfix"></div>

        @include('documents.partials.new-comment-form', ['route' => ['documents.comments.storeReply', $comment->annotatable_id, $comment->id], 'message' => 'messages.document.add_reply'])
    @endif

    <div class="comment-replies hide">
        @if ($comment->comments()->count() > 0)
            @each('documents/partials/comment-div', $comment->comments()->get(), 'comment')
        @endif
    </div>
</div>

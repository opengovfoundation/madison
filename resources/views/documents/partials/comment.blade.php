<div class="comment" id="comment-{{ $comment->id }}">
    <h4>
        <strong>{{ $comment->user->display_name }}</strong>
        <span class="small">{{ $comment->created_at->diffForHumans() }}</span>
    </h4>
    <p>{{ $comment->annotationType->content }}</p>
    <span class="likes">
        <span class="fa fa-thumbs-up" aria-hidden="true"></span>
        {{ $comment->likes_count }}
    </span>
    <span class="flags">
        <span class="fa fa-flag" aria-hidden="true"></span>
        {{ $comment->flags_count }}
    </span>
    <span class="replies">
        <span class="fa fa-comments" aria-hidden="true"></span>
        @if ($comment->comments()->count() > 0)
            <a href="#" data-comment-id="{{ $comment->id }}">
                {{ $comment->comments()->count() }}
            </a>
        @else
            {{ $comment->comments()->count() }}
        @endif
    </span>

    @if ($comment->comments()->count() > 0)
        <div class="comment-replies hide">
            <hr>
            @each ('documents/partials/comment', $comment->comments()->get(), 'comment')
        </div>
    @endif
    <hr>
</div>

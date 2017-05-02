<table class="table">
    <thead>
        <tr>
            <th>@lang('messages.user.user')</th>
            <th>@lang('messages.document.comment')</th>
            <th>@lang('messages.document.like')</th>
            <th>@lang('messages.document.flag')</th>
            <th>@lang('messages.document.replies')</th>
            <th>@lang('messages.created')</th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($comments as $comment)
            <tr id="comment-{{ $comment->id }}">
                <td>{{ $comment->user->display_name }}</td>
                <td>
                    @if (!$comment->isHidden())
                        <a href="{{ $comment->getLink() }}">
                    @endif
                    {{ str_limit($comment->annotationType->content, 100, ' ...') }}
                    @if (!$comment->isHidden())
                        </a>
                    @endif
                </td>
                <td>{{ $comment->likes_count }}</td>
                <td>{{ $comment->flags_count }}</td>
                <td>{{ $comment->comments()->withoutGlobalScope('visible')->notHidden()->count() }}</td>
                <td>{{ $comment->created_at->diffForHumans() }}</td>
                <td>
                    <div class="btn-group" role="group">
                        {{ Form::open(['route' => ['documents.comments.storeHidden', $document, $comment], 'method' => 'post']) }}
                            @if ($comment->isHidden())
                                <button type="submit" class="btn btn-default btn-xs" disabled>
                                    {{ trans('messages.document.hidden_comment') }}
                                </button>
                            @else
                                <button type="submit" class="btn btn-default btn-xs">
                                    {{ trans('messages.document.hide_comment') }}
                                </button>
                            @endif
                        {{ Form::close() }}
                    </div>
                </td>
                <td>
                    <div class="btn-group" role="group">
                        {{ Form::open(['route' => ['documents.comments.storeResolve', $document, $comment], 'method' => 'post']) }}
                            @if ($comment->isResolved())
                                <button type="submit" class="btn btn-default btn-xs" disabled>
                                    {{ trans('messages.document.resolved_comment') }}
                                </button>
                            @else
                                <button type="submit" class="btn btn-default btn-xs">
                                    {{ trans('messages.document.resolve_comment') }}
                                </button>
                            @endif
                        {{ Form::close() }}
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

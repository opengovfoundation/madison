<table class="table">
    <thead>
        <tr>
            <th>@lang('messages.user.user')</th>
            <th>@lang('messages.type')</th>
            <th>@lang('messages.document.comment')</th>
            <th>@lang('messages.document.like')</th>
            <th>@lang('messages.document.flag')</th>
            <th>@lang('messages.document.replies')</th>
            <th>@lang('messages.created')</th>
            <th>@lang('messages.actions')</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($comments as $comment)
            <tr>
                <td>{{ $comment->user->display_name }}</td>
                <td>{{ $comment->isNote() ? trans('messages.document.note') : trans('messages.document.comment') }}</td>
                <td>{{ str_limit($comment->annotationType->content, 100, ' ...') }}</td>
                <td>{{ $comment->likes_count }}</td>
                <td>{{ $comment->flags_count }}</td>
                <td>{{ $comment->comments()->withoutGlobalScope('visible')->notHidden()->count() }}</td>
                <td>{{ $comment->created_at->diffForHumans() }}</td>
                <td>
                    @if (!$comment->isHidden())
                        <div class="btn-group" role="group">
                            <a href="{{ $comment->getLink() }}" class="btn btn-default">
                                {{ trans('messages.view') }}
                            </a>
                        </div>
                    @endif
                    <div class="btn-group" role="group">
                        {{ Form::open(['route' => ['documents.comments.storeHidden', $document, $comment], 'method' => 'post']) }}
                            @if ($comment->isHidden())
                                <button type="submit" class="btn btn-default" disabled="true">
                                    {{ trans('messages.document.hidden_comment') }}
                                </button>
                            @else
                                <button type="submit" class="btn btn-default">
                                    {{ trans('messages.document.hide_comment') }}
                                </button>
                            @endif
                        {{ Form::close() }}
                    </div>
                    <div class="btn-group" role="group">
                        {{ Form::open(['route' => ['documents.comments.storeResolve', $document, $comment], 'method' => 'post']) }}
                            @if ($comment->isResolved())
                                <button type="submit" class="btn btn-default" disabled="true">
                                    {{ trans('messages.document.resolved_comment') }}
                                </button>
                            @else
                                <button type="submit" class="btn btn-default">
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

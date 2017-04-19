<div class="new-comment-form">
    <div class="collapsed-content">
        <div class="media">
            <div class="media-left">
                <img class="media-object img-circle" alt="{{ trans('messages.user.avatar_alt_text') }}" src="{{ Auth::user()->avatar }}">
            </div>
            <div class="media-body media-middle">
                <button class="btn btn-link new-comment-form-toggle" onclick="toggleNewCommentForm(this)">
                    @lang($message)
                </button>
            </div>
        </div>
    </div>

    <div class="expanded-content hidden">
        <div class="media">
            <div class="media-left">
                <img class="media-object" alt="{{ trans('messages.user.avatar_alt_text') }}" src="{{ Auth::user()->avatar }}">
            </div>
            <div class="media-body media-middle">
                {{ Auth::user()->display_name }}
            </div>
        </div>

        {{ Form::open(['route' => $route, 'class' => 'comment-form']) }}
            {{ Form::mInput(
                'textarea',
                'text',
                trans($message),
                null,
                [ 'rows' => 3, 'label-sr-only' => true ]
            ) }}
            <button type="button" class="btn btn-default" onclick="toggleNewCommentForm(this)">@lang('messages.cancel')</button>
            {{ Form::mSubmit() }}
        {{ Form::close() }}
    </div>
</div>

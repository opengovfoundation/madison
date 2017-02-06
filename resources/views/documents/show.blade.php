@extends('layouts.app')

@push('styles')
    <link href="{{ elixir('css/annotator.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="page-header">
        <h1>{{ $document->title }}</h1>
    </div>

    @include('components.errors')

    @if ($document->discussion_state !== \App\Models\Doc::DISCUSSION_STATE_HIDDEN)
        <div class="row">
            <div class="col-md-12">
                <p>
                    <div class="document-stats pull-right lead">
                        <span class="participants-count">
                            <strong>{{ trans('messages.document.participants') }}:</strong> {{ $userCount }}
                        </span>
                        <span class="comments-count">
                            <strong>{{ trans('messages.document.comments') }}</strong>: {{ $commentCount }}
                        </span>
                        <span class="notes-count">
                            <strong>{{ trans('messages.document.notes') }}</strong>: {{ $noteCount }}
                        </span>
                    </div>

                    <div class="btn-group" role="group">
                            {{ Form::open(['route' => ['documents.support', $document], 'method' => 'put']) }}
                                <input type="hidden" name="support" value="1">

                                @if ($userSupport === true)
                                    <button type="submit" class="btn btn-success">
                                        {{ trans('messages.document.supported') }} ({{ $supportCount }})
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-default">
                                        {{ trans('messages.document.support') }} ({{ $supportCount }})
                                    </button>
                                @endif
                            {{ Form::close() }}
                    </div>
                    <div class="btn-group" role="group">
                            {{ Form::open(['route' => ['documents.support', $document], 'method' => 'put']) }}
                                <input type="hidden" name="support" value="0">
                                @if ($userSupport === false)
                                    <button type="submit" class="btn btn-warning">
                                        {{ trans('messages.document.opposed') }} ({{ $opposeCount }})
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-default">
                                        {{ trans('messages.document.oppose') }} ({{ $opposeCount }})
                                    </button>
                                @endif
                            {{ Form::close() }}
                    </div>
                </p>
            </div>
        </div>
    @endif

    @if (!empty($document->introtext))
        <div class="panel panel-default">
            <div class="panel-heading">@lang('messages.document.introtext')</div>
            <div class="panel-body">
                {!! $document->introtext !!}
            </div>
        </div>
    @endif

    @if ($document->discussion_state !== \App\Models\Doc::DISCUSSION_STATE_HIDDEN)
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#content" role="tab" data-toggle="tab">@lang('messages.document.content')</a>
            </li>
            <li role="presentation">
                <a href="#comments" role="tab" data-toggle="tab">@lang('messages.document.comments')</a>
            </li>
        </ul>
    @endif

    <div class="tab-content">
        <div class="active tab-pane row" id="content" role="tabpanel">
            <section id="page_content" class="col-md-8">
                {!! $documentPages->first()->rendered() !!}
            </section>

            <aside class="annotation-container col-md-4"></aside>
        </div>

        @if ($document->discussion_state !== \App\Models\Doc::DISCUSSION_STATE_HIDDEN)
            <div class="tab-pane row comments" id="comments" role="tabpanel">
                <section class="col-md-8">
                    @if ($document->discussion_state === \App\Models\Doc::DISCUSSION_STATE_OPEN)
                        @if (Auth::user())
                            {{ Form::open(['route' => ['documents.comments.store', $document]]) }}
                                {{ Form::mInput(
                                    'textarea',
                                    'text',
                                    trans('messages.document.add_comment'),
                                    null,
                                    [ 'rows' => 3 ]
                                ) }}
                                {{ Form::mSubmit() }}
                            {{ Form::close() }}
                        @else
                            {{ Html::linkRoute('login', trans('messages.document.login_to_comment')) }}
                        @endif
                        <hr>
                    @endif

                    @each('documents/partials/comment', $comments, 'comment')
                    @include('components.pagination', ['collection' => $comments])
                </section>

                <section class="col-md-4"></section>
            </div>
        @endif
    </div>

    {{ $documentPages->appends(request()->query())->fragment('page_content')->links() }}

    @push('scripts')
        <script src="{{ elixir('js/annotator-madison.js') }}"></script>
        <script src="{{ elixir('js/document.js') }}"></script>
        <script>
            loadTranslations([
                'messages.close',
                'messages.document.add_reply',
                'messages.document.collaborators_count',
                'messages.document.flag',
                'messages.document.like',
                'messages.document.note',
                'messages.document.note_edit_explanation_prompt',
                'messages.document.note_reply',
                'messages.document.notes',
                'messages.document.replies_count',
                'messages.edit',
                'messages.none',
                'messages.permalink',
                'messages.submit'
            ])
            .done(function () {
                @if ($document->discussion_state !== \App\Models\Doc::DISCUSSION_STATE_HIDDEN)
                    loadAnnotations(
                        "#page_content",
                        ".annotation-container",
                        {{ $document->id }},
                        {{ request()->user() ? request()->user()->id : 'null' }},
                        {{ $document->discussion_state === \App\Models\Doc::DISCUSSION_STATE_CLOSED ? 1 : 0 }}
                    );

                    // race-y with loading annotaions, so it's called again
                    // in annotator-madison.js after annotator.js has loaded
                    // it's stuff
                    revealComment({{ $document->id }});
                    window.onhashchange = revealComment.bind(this, {{$document->id }});

                    if (window.getQueryParam('comment_page')) {
                        showComments();
                    }

                    $('.activity-actions a.comments').click(function(e) {
                        e.preventDefault();
                        let commentId = $(e.target).data('comment-id');
                        toggleCommentReplies(commentId);
                    });

                    $('.comment a.action-link').click(function(e) {
                        e.preventDefault();
                        $(e.target).trigger('madison.addAction');
                    });
                @endif
            });
        </script>
    @endpush
@endsection

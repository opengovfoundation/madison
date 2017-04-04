@extends('layouts.app')

@section('pageTitle', $document->title)

@push('styles')
    <link href="{{ elixir('css/annotator.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    @include('components.errors')

    @can('viewManage', $document)
        <a href="{{ route('documents.manage.comments', $document) }}" class="btn btn-default pull-right">@lang('messages.document.moderate')</a>
    @endcan

    <div class="jumbotron">
        <h1>{{ $document->title }}</h1>
        <p class="sponsors">
            @lang('messages.document.sponsoredby', ['sponsors' => $document->sponsors->implode('display_name', ', ')])
        </p>

        @if (!empty($document->introtext))
            <hr>
            <div class="introtext">
                {!! $document->introtext !!}
            </div>
        @endif
    </div>

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
            <div class="col-md-10">
                @if ($document->discussion_state !== \App\Models\Doc::DISCUSSION_STATE_HIDDEN)
                    <div class="support-btns pull-right text-center">
                        <div>
                            <small>@lang('messages.document.support_prompt')</small>
                        </div>
                        <div class="btn-group support-btn" role="group">
                                {{ Form::open(['route' => ['documents.support', $document], 'method' => 'put']) }}
                                    <input type="hidden" name="support" value="1">

                                    <button type="submit" class="btn btn-primary btn-xs {{ $userSupport === true ? 'active' : '' }}">
                                        <i class="fa fa-thumbs-up" aria-hidden="true"></i>
                                        @if ($userSupport === true)
                                            {{ trans('messages.document.supported') }}
                                        @else
                                            {{ trans('messages.document.support') }}
                                        @endif
                                        ({{ $supportCount }})
                                    </button>
                                {{ Form::close() }}
                        </div>
                        <div class="btn-group oppose-btn" role="group">
                                {{ Form::open(['route' => ['documents.support', $document], 'method' => 'put']) }}
                                    <input type="hidden" name="support" value="0">
                                    <button type="submit" class="btn btn-primary btn-xs {{ $userSupport === false ? 'active' : '' }}">
                                        <i class="fa fa-thumbs-down" aria-hidden="true"></i>
                                        @if ($userSupport === false)
                                                {{ trans('messages.document.opposed') }}
                                        @else
                                                {{ trans('messages.document.oppose') }}
                                        @endif
                                        ({{ $opposeCount }})
                                    </button>
                                {{ Form::close() }}
                        </div>
                    </div>
                @endif

                <section id="page_content">
                    {!! $documentPages->first()->rendered() !!}
                </section>
            </div>

            <aside class="annotation-container col-md-2"></aside>
        </div>

        @if ($document->discussion_state !== \App\Models\Doc::DISCUSSION_STATE_HIDDEN)
            <div class="tab-pane row comments" id="comments" role="tabpanel">
                <section class="col-md-8">
                    @if ($document->discussion_state === \App\Models\Doc::DISCUSSION_STATE_OPEN)
                        @if (Auth::user())
                            {{ Form::open(['route' => ['documents.comments.store', $document], 'class' => 'comment-form']) }}
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

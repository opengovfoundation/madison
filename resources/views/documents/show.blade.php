@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1>{{ $document->title }}</h1>
    </div>

    @include('components.errors')

    @if (!empty($document->introtext))
        <div class="panel panel-default">
            <div class="panel-heading">@lang('messages.document.introtext')</div>
            <div class="panel-body">
                {!! $document->introtext !!}
            </div>
        </div>
    @endif

    <div class="row">
        <section id="page_content" class="col-md-8">
            {!! $pages->first()->rendered() !!}
        </section>

        <aside class="annotation-container col-md-4">
            <h2>@lang('messages.document.notes')</h2>
        </aside>
    </div>

    {{ $pages->appends(request()->query())->fragment('page_content')->links() }}

    @push('scripts')
        <script src="{{ elixir('js/annotator-madison.js') }}"></script>
        <script src="{{ elixir('js/document.js') }}"></script>
        <script>
            loadTranslations([
                'messages.close',
                'messages.edit',
                'messages.none',
                'messages.document.note',
                'messages.document.note_edit_explanation_prompt',
                'messages.document.collaborators_count',
                'messages.document.replies_count',
                'messages.document.notes'
            ])
            .done(function () {
                loadAnnotations(
                    "#page_content",
                    ".annotation-container",
                    {{ $document->id }},
                    {{ request()->user() ? request()->user()->id : 'null' }},
                    {{ $document->discussionState === \App\Models\Doc::DISCUSSION_STATE_CLOSED ? 1 : 0 }}
                );
        });
        </script>
    @endpush
@endsection

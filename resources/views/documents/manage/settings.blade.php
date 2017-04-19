@extends('layouts.app')

@section('pageTitle', trans('messages.document.edit'))

@section('content')
    <div class="page-header">

        <a href="{{ route('documents.manage.comments', $document) }}" class="btn btn-default pull-right">
            @lang('messages.document.manage_comments')
        </a>

        <h1>@lang('messages.document.edit')</h1>
        @include('components.breadcrumbs.document', ['sponsor' => $document->sponsors()->first(), 'document' => $document])
    </div>

    @include('components.errors')

    {{ Form::model($document, ['route' => ['documents.update', $document], 'method' => 'put', 'files' => true, 'class' => 'edit-document']) }}
        <fieldset class="row" {{ Auth::user()->cant('update', $document) ? 'disabled' : '' }}>
            <div class="col-md-8">
                {{ Form::mInput('textarea', 'title', trans('messages.document.title'), null, ['label-sr-only' => true, 'rows' => 1]) }}
                {{ Form::mInput('textarea', 'introtext', trans('messages.document.introtext'), null, ['rows' => 2]) }}

                <input type="hidden" name="page" value="{{ request()->input('page', 1) }}" />
                {{ Form::mInput('textarea', 'page_content', trans('messages.document.content'), $pages->first()->content, ['rows' => 20]) }}
                <div class="document-pages-toolbar">
                    {{ $pages->appends(request()->query())->fragment('page_content')->links() }}

                    {{-- Submits the hidden add page form --}}
                    <button
                        type="button"
                        class="btn btn-default pull-right add-page"
                        onclick="event.preventDefault();document.getElementById('add-page-form').submit();">
                        @lang('messages.document.add_page')
                    </button>
                </div>
            </div>

            <div class="col-md-4">
                <div class="settings-sidebar">
                    <button type="submit" class="btn btn-primary btn-block">@lang('messages.document.save')</button>
                    <a href="{{ $document->url }}" class="btn btn-default btn-block">@lang('messages.document.view')</a>

                    <hr>

                    {{ Form::mSelect(
                            'publish_state',
                            trans('messages.document.publish_state'),
                            collect($publishStates)->mapWithKeys_v2(function ($item) {return [$item => trans('messages.document.publish_states.'.$item)]; })->toArray(),
                            null,
                            [],
                            trans('messages.document.publish_state_help')
                            )
                    }}
                    {{ Form::mSelect(
                            'discussion_state',
                            trans('messages.document.discussion_state'),
                            collect($discussionStates)->mapWithKeys_v2(function ($item) {return [$item => trans('messages.document.discussion_states.'.$item)];})->toArray(),
                            null,
                            [],
                            trans('messages.document.discussion_state_help')
                            )
                    }}
                    {{ Form::mInput('text', 'slug', trans('messages.document.slug'), null, [], trans('messages.document.slug_help')) }}
                </div>
            </div>
        </fieldset>
    {{ Form::close() }}


    {{-- Hidden form to submit in order to add a blank page to document --}}
    {{ Form::open([
           'route' => ['documents.pages.store', $document],
           'method' => 'post',
           'style' => 'display: none;',
           'id' => 'add-page-form',
           ])
    }}
    {{ Form::close() }}
@endsection

@push('scripts')
    <script>
        autoHeightTextarea($('textarea[name="title"]')[0]);
        autoHeightTextarea($('textarea[name="introtext"]')[0]);

        // Only set side affix and auto content height on md+ screens
        if (['xs', 'sm'].indexOf(window.screenSize()) === -1) {
            let $settingsSidebar = $('.settings-sidebar');

            $settingsSidebar.affix({
                offset: { top: $settingsSidebar.parent().position().top - 10 }
            });

            autoHeightTextarea($('textarea[name="page_content"]')[0]);
        }
    </script>
@endpush

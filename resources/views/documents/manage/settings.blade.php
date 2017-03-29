@extends('documents.manage')

@section('pageTitle', trans('messages.settings'))

@section('manage_content')
    {{ Form::model($document, ['route' => ['documents.update', $document], 'method' => 'put', 'files' => true]) }}
        <fieldset class="row" {{ Auth::user()->cant('update', $document) ? 'disabled' : '' }}>
            <div class="col-md-8">
                {{ Form::mInput('text', 'title', trans('messages.document.title')) }}
                {{ Form::mInput('textarea', 'introtext', trans('messages.document.introtext'), null, ['rows' => 2]) }}

                {{ Form::mInput('file', 'featured-image', trans('messages.document.featured_image')) }}
                @if ($document->featuredImage)
                    <img src="{{ $document->getFeaturedImageUrl() }}"/>

                    {{-- Submits the hidden remove image form --}}
                    <button
                        type="button"
                        class="btn btn-default"
                        onclick="event.preventDefault();document.getElementById('remove-featured-image-form').submit();">
                        @lang('messages.document.featured_image_remove')
                    </button>
                @endif

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

    {{-- Hidden form to delete featured image of document --}}
    {{ Form::open([
           'route' => ['documents.images.destroy', $document, $document->featuredImage],
           'method' => 'delete',
           'style' => 'display: none;',
           'id' => 'remove-featured-image-form',
           ])
    }}
    {{ Form::close() }}
@endsection

@extends('layouts.app')

@section('pageTitle', trans('messages.document.edit'))

@section('content')
    <div class="page-header">
        <h1>{{ trans('messages.document.edit') }}</h1>
    </div>

    @include('components.errors')

    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#content" aria-controls="content" role="tab" data-toggle="tab">@lang('messages.edit')</a></li>
        <li role="presentation"><a href="#misc" aria-controls="misc" role="tab" data-toggle="tab">@lang('messages.misc')</a></li>
    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="content">
            {{ Form::model($document, ['route' => ['documents.update', $document], 'method' => 'put', 'files' => true]) }}
                {{ Form::mInput('text', 'title', trans('messages.document.title')) }}
                {{ Form::mInput('text', 'slug', trans('messages.document.slug'), null, [], trans('messages.document.slug_help')) }}
                {{ Form::mInput('textarea', 'introtext', trans('messages.document.introtext')) }}

                {{ Form::mInput('checkbox', 'featured', trans('messages.document.featured'), $document->featured, request()->user()->isAdmin() ? [] : ['disabled' => true]) }}
                {{ Form::mInput('file', 'featured-image', trans('messages.document.featured_image'), null, request()->user()->isAdmin() ? [] : ['disabled' => true]) }}
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
                {{ Form::mSelect(
                        'sponsor_id',
                        trans('messages.document.sponsor'),
                        $sponsors->mapWithKeys_v2(function ($item) {return [$item->id => $item->display_name]; })->toArray(),
                        $document->sponsors->map(function($sponsor) { return $sponsor->id; })->first()
                        )
                }}
                {{ Form::mSelect(
                        'category_id[]',
                        trans('messages.document.category'),
                        $categories->mapWithKeys_v2(function ($item) {return [$item->id => $item->name]; })->toArray(),
                        $document->categories->map(function($cat) { return $cat->id; })->toArray(),
                        ['multiple' => true]
                        )
                }}
                <input type="hidden" name="page" value="{{ request()->input('page', 1) }}" />
                {{ Form::mInput('textarea', 'page_content', trans('messages.document.content'), $pages->first()->content) }}
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

                {{ Form::mSubmit() }}

            {{ Form::close() }}
        </div>
        <div role="tabpanel" class="tab-pane" id="misc">
            <a href="{{ route('documents.comments.index', [$document, 'download' => 'csv']) }}">@lang('messages.document.download_comments_csv')</a>
        </div>
    </div>


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

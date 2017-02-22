@extends('layouts.app')

@section('pageTitle', trans('messages.document.moderate', ['document' => $document->title]))

@section('content')
    <div class="page-header">
        <h1>@lang('messages.document.moderate_document', ['document' => $document->title])</h1>
    </div>

    @include('components.errors')

    @if ($unhandledComments->count() > 0)
        <h3>@lang('messages.document.comments_unhandled')</h3>
        @include('documents.partials.comment_table', ['comments' => $unhandledComments, 'document' => $document])
    @endif

    @if ($handledComments->count() > 0)
        <h3>@lang('messages.document.comments_handled')</h3>
        @include('documents.partials.comment_table', ['comments' => $handledComments, 'document' => $document])
    @endif
@endsection

@extends('layouts.app')

@section('pageTitle', trans('messages.document.manage_comments'))

@section('content')
    <div class="page-header">
        <a href="{{ route('documents.manage.settings', $document) }}" class="btn btn-default pull-right">
            @lang('messages.document.edit')
        </a>

        <h1>@lang('messages.document.manage_comments')</h1>
        @include('components.breadcrumbs.document', ['sponsor' => $document->sponsors()->first(), 'document' => $document])
    </div>

    @include('components.errors')

    <div class="row">
        <div class="col-md-12">
            <div>
                <p class="pull-right">
                    <a href="{{ route('documents.comments.index', [$document, 'download' => 'csv', 'all' => true]) }}" class="btn btn-primary">
                        @lang('messages.document.download_comments_csv')
                    </a>
                </p>
                <h2>{{ $document->title }}</h2>
                <hr>
            </div>

            <div class="panel panel-default unhandled">
                <div  class="panel-heading">
                    <h3 class="panel-title">@lang('messages.document.comments_unhandled')</h3>
                </div>
                <div class="panel-body">
                    @if ($unhandledComments->count() > 0)
                        @include('documents.partials.comment_table', ['comments' => $unhandledComments, 'document' => $document])
                    @else
                        <div class="text-center">
                            @lang('messages.none')
                        </div>
                    @endif
                </div>
            </div>

            <div class="panel panel-default handled">
                <div  class="panel-heading">
                    <h3 class="panel-title">@lang('messages.document.comments_handled')</h3>
                </div>
                <div class="panel-body">
                    @if ($handledComments->count() > 0)
                        @include('documents.partials.comment_table', ['comments' => $handledComments, 'document' => $document])
                    @else
                        <div class="text-center">
                            @lang('messages.none')
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

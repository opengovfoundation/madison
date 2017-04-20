@extends('layouts.app')

@section('pageTitle', trans('messages.sponsor.page_title_documents', ['sponsorName' => $sponsor->display_name]))

@section('content')
    @include('components.breadcrumbs.sponsor', ['sponsor' => $sponsor])

    <div class="page-header">
        @if ($sponsor->userCanCreateDocument(Auth::user()))
            @if ($showingDeleted)
                <a href="{{ route('sponsors.documents.index', $sponsor) }}" class="btn btn-link pull-right">
                    @lang('messages.document.view_documents')
                </a>
            @else
                <a href="{{ route('sponsors.documents.index', ['sponsor' =>  $sponsor, 'deleted' => true]) }}" class="btn btn-link pull-right">
                    @lang('messages.document.view_deleted')
                </a>
            @endif
        @endif

        <h1>{{ $sponsor->display_name }}</h1>
    </div>

    @include('components.errors')

    <div class="row">
        @include('sponsors.partials.sponsor-sidebar', ['sponsor' => $sponsor])
        <div class="col-md-9">
            <table class="table">
                <thead>
                    <tr>
                        <th>@lang('messages.document.title')</th>
                        <th>@lang('messages.created')</th>
                        <th>@lang('messages.document.publish_state_short')</th>
                        <th>@lang('messages.document.discussion_state_short')</th>
                        <th>@lang('messages.document.comments')</th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($documents as $document)
                        <tr id="document-{{ $document->id }}">
                            <td>
                                @if ($document->canUserView(Auth::user()) && !$document->trashed())
                                    <a href="{{ route('documents.show', $document) }}">{{ $document->title }}</a>
                                @else
                                    {{ $document->title }}
                                @endif
                            </td>
                            <td>
                                @include('components/date', [
                                    'datetime' => $document->created_at,
                                ])
                            </td>
                            <td>{{ trans('messages.document.publish_states.'.$document->publish_state) }}</td>
                            <td>{{ trans('messages.document.discussion_states.'.$document->discussion_state) }}</td>
                            <td>
                                {{ $document->all_comments_count }}
                                <a href="{{ route('documents.comments.index', [$document, 'download' => 'csv', 'all' => true]) }}" title="{{ trans('messages.document.download_comments_csv') }}">
                                    <i class="fa fa-download" aria-hidden="true"></i>
                                </a>
                            </td>
                            <td>
                                @can('viewManage', $document)
                                    <a href="{{ route('documents.manage.settings', $document) }}"
                                        title="@lang('messages.document.manage')">

                                        <i class="fa fa-pencil"></i>
                                    </a>
                                @endif
                            </td>
                            <td>
                                @can('delete', $document)
                                    <div class="btn-group" role="group">
                                        {{ Form::open(['route' => ['documents.destroy', $document], 'method' => 'delete']) }}
                                            <button type="submit" class="btn btn-xs btn-link delete-document">
                                                <i class="fa fa-close"></i>
                                            </button>
                                        {{ Form::close() }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if ($document->trashed() && Auth::user()->can('restore', $document))
                                    <div class="btn-group" role="group">
                                        <a class="btn btn-xs btn-link restore-document" href="{{ route('documents.restore', $document) }}">
                                            <i class="fa fa-undo"></i>
                                        </a>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="text-center">
                @include('components.pagination', ['collection' => $documents])
            </div>

            <hr>

            @if (Auth::user() && $sponsor->userCanCreateDocument(Auth::user()))
                <button class="btn btn-primary new-document" data-toggle="modal" data-target="#new-document-modal">
                    @lang('messages.document.new')
                </button>
            @endif
        </div>
    </div>

    @if (Auth::user() && $sponsor->userCanCreateDocument(Auth::user()))
        <div class="modal fade" id="new-document-modal" tabindex="-1" role="dialog" aria-labelledby="new-document-modal-label">
            <div class="modal-dialog" role="document">
                {{ Form::open(['route' => ['documents.store'], 'class' => 'modal-content']) }}
                    <div class="modal-header">
                        <h2 class="modal-title" id="new-document-modal-label">@lang('messages.document.new')</h2>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="sponsor_id" value="{{ $sponsor->id }}">
                        {{ Form::mInput('text', 'title', trans('messages.document.title')) }}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
                        {{ Form::mSubmit() }}
                    </div>
                {{ Form::close() }}
            </div>
        </div>
    @endif

    @push('scripts')
        <script>
            $('#new-document-modal').on('shown.bs.modal', function(e) {
                $(e.target).find('input[name=title]').focus();
            });
        </script>
    @endpush

@endsection

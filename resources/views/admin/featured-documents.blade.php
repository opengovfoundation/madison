@extends('layouts.app')

@section('pageTitle', trans('messages.admin.featured_documents'))

@section('content')
    @include('components.breadcrumbs.admin')

    <div class="page-header">
        <h1>{{ trans('messages.admin.admin_label', ['page' => trans('messages.admin.featured_documents')]) }}</h1>
    </div>

    @include('components.errors')

    <div class="row">
        @include('admin.partials.admin-sidebar')

        <div class="col-md-9">
            <table class="table">
                <thead>
                    <tr>
                        <th>@lang('messages.order')</th>
                        <th>@lang('messages.document.title')</th>
                        <th>@lang('messages.document.publish_state')</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($documents as $document)
                        <tr id="document-{{ $document->id }}">
                            <td>
                                {{ Form::open(['route' => ['admin.featured-documents.update', $document->id], 'method' => 'put', 'class' => 'inline']) }}
                                    <input type="hidden" name="action" value="up">
                                    <button type="submit" class="btn btn-xs btn-link up" {!! $loop->first ? 'disabled' : null !!}>
                                        <i class="fa fa-arrow-up"></i>
                                    </button>
                                {{ Form::close() }}

                                {{ Form::open(['route' => ['admin.featured-documents.update', $document->id], 'method' => 'put', 'class' => 'inline']) }}
                                    <input type="hidden" name="action" value="down">
                                    <button type="submit" class="btn btn-xs btn-link down" {!! $loop->last ? 'disabled' : null !!}>
                                        <i class="fa fa-arrow-down"></i>
                                    </button>
                                {{ Form::close() }}
                            </td>

                            <td>{{ $document->title }}</td>

                            <td>{{ trans('messages.document.publish_states.'.$document->publish_state) }}</td>

                            <td class="text-right">
                                {{ Form::open(['route' => ['admin.featured-documents.update', $document->id], 'method' => 'put']) }}
                                    <input type="hidden" name="action" value="remove">
                                    <button type="submit" class="btn btn-xs btn-link unfeature">
                                        <i class="fa fa-times"></i>
                                    </button>
                                {{ Form::close() }}
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>

            <hr>

            {{ Form::open(['route' => 'admin.featured-documents.add', 'method' => 'post', 'class' => 'add-featured-document row']) }}
                <div class="col-sm-9">
                    {{ Form::mSelect(
                            'add_featured_doc_id',
                            trans('messages.admin.add_featured_document'),
                            collect($nonFeaturedDocuments)->mapWithKeys_v2(function ($item) {return [$item->id => $item->title]; })->toArray(),
                            null,
                            ['label-sr-only' => true]
                            )
                    }}
                </div>
                <div class="col-sm-3">
                    {{ Form::mSubmit(trans('messages.admin.add_featured_document')) }}
                </div>
            {{ Form::close() }}
        </div>
    </div>

@endsection

@extends('layouts.app')

@section('pageTitle', trans('messages.admin.featured_documents'))

@section('content')

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
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($documents as $document)
                        <tr>
                            <td>
                                {{ Form::open(['route' => ['admin.featured-documents.update', $document->id], 'method' => 'put', 'class' => 'inline']) }}
                                    <input type="hidden" name="action" value="up">
                                    @if (!$loop->first)
                                        <button type="submit" class="btn btn-xs btn-default">
                                            <i class="fa fa-arrow-up"></i>
                                        </button>
                                    @else
                                        <button disabled="disabled" class="btn btn-xs btn-default">
                                            <i class="fa fa-arrow-up"></i>
                                        </button>
                                    @endif
                                {{ Form::close() }}

                                {{ Form::open(['route' => ['admin.featured-documents.update', $document->id], 'method' => 'put', 'class' => 'inline']) }}
                                    <input type="hidden" name="action" value="down">
                                    @if (!$loop->last)
                                        <button type="submit" class="btn btn-xs btn-default">
                                            <i class="fa fa-arrow-down"></i>
                                        </button>
                                    @else
                                        <button disabled="disabled" class="btn btn-xs btn-default">
                                            <i class="fa fa-arrow-down"></i>
                                        </button>
                                    @endif
                                {{ Form::close() }}
                            </td>

                            <td>{{ $document->title }}</td>

                            <td>{{ trans('messages.document.publish_states.'.$document->publish_state) }}</td>

                            <td class="text-right">
                                {{ Form::open(['route' => ['admin.featured-documents.update', $document->id], 'method' => 'put']) }}
                                    <input type="hidden" name="action" value="remove">
                                    <button type="submit" class="btn btn-xs btn-danger">
                                        <i class="fa fa-times"></i>
                                    </button>
                                {{ Form::close() }}
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection

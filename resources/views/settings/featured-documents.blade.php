@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1>{{ trans('messages.settings.featured_documents') }}</h1>
    </div>

    @include('components.errors')

    <table class="table">
        <thead>
            <tr>
                <th>@lang('messages.id')</th>
                <th>@lang('messages.document.title')</th>
                <th>@lang('messages.created')</th>
                <th>@lang('messages.document.sponsor')</th>
                <th>@lang('messages.document.publish_state')</th>
                <th>@lang('messages.actions')</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($documents as $document)
                <tr>
                    <td>{{ $document->id }}</td>
                    <td>{{ $document->title }}</td>
                    <td>{{ $document->created_at->toDateTimeString() }}</td>
                    <td>
                        {{ $document->sponsors->shift()->display_name }}
                        @if ($document->sponsors->count() > 1)
                            @lang('messages.document.sponsor_others')
                        @endif
                    </td>
                    <td>{{ trans('messages.document.publish_states.'.$document->publish_state) }}</td>
                    <td>
                        <div class="btn-toolbar" role="toolbar">
                            <div class="btn-group" role="group">
                                {{ Form::open(['route' => ['settings.featured-documents.update', $document->id], 'method' => 'put']) }}
                                    <input type="hidden" name="action" value="up">
                                    @if (!$loop->first)
                                        <button type="submit" class="btn btn-default">
                                            <i class="fa fa-arrow-up"></i>
                                        </button>
                                    @else
                                        <button disabled="disabled" class="btn btn-default">
                                            <i class="fa fa-arrow-up"></i>
                                        </button>
                                    @endif
                                {{ Form::close() }}
                            </div>

                            <div class="btn-group" role="group">
                                {{ Form::open(['route' => ['settings.featured-documents.update', $document->id], 'method' => 'put']) }}
                                    <input type="hidden" name="action" value="down">
                                    @if (!$loop->last)
                                        <button type="submit" class="btn btn-default">
                                            <i class="fa fa-arrow-down"></i>
                                        </button>
                                    @else
                                        <button disabled="disabled" class="btn btn-default">
                                            <i class="fa fa-arrow-down"></i>
                                        </button>
                                    @endif
                                {{ Form::close() }}
                            </div>

                            <div class="btn-group" role="group">
                                {{ Form::open(['route' => ['settings.featured-documents.update', $document->id], 'method' => 'put']) }}
                                    <input type="hidden" name="action" value="remove">
                                    <button type="submit" class="btn btn-default">
                                        {{ trans('messages.remove') }}
                                    </button>
                                {{ Form::close() }}
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

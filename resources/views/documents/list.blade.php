@extends('layouts.app')

@section('pageTitle', trans('messages.document.list'))

@section('content')
    <div class="page-header">
        <button type="button" class="btn btn-default pull-right" data-toggle="modal" data-target="#queryModal">@lang('messages.advanced_search')</button>
        <h1>{{ trans('messages.document.list') }}</h1>
    </div>

    @include('components.errors')

    <div class="modal fade" id="queryModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            {{ Form::open(['route' => 'documents.index', 'method' => 'get']) }}
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">@lang('messages.advanced_search')</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                {{ Form::mInput('text', 'q', trans('messages.search.title')) }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                {{ Form::mSelect(
                                       'sponsor_id[]',
                                       trans('messages.document.sponsor'),
                                       $sponsors->mapWithKeys_v2(function ($item) {return [$item->id => $item->display_name]; })->toArray(),
                                       null,
                                       ['multiple' => true]
                                       )
                                }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                {{ Form::mSelect(
                                       'order',
                                       trans('messages.order_by'),
                                       [
                                           'created_at' => trans('messages.created'),
                                           'updated_at' => trans('messages.updated'),
                                           'title' => trans('messages.document.title'),
                                           'activity' => trans('messages.document.activity'),
                                           'relevance' => trans('messages.relevance'),
                                       ])
                                }}
                            </div>
                            <div class="col-md-4">
                                {{ Form::mSelect(
                                       'order_dir',
                                       trans('messages.order_by_direction'),
                                       [
                                           'DESC' => trans('messages.order_by_dir_desc'),
                                           'ASC' => trans('messages.order_by_dir_asc')
                                       ])
                                }}
                            </div>
                            <div class="col-md-4">
                                {{ Form::mSelect(
                                       'limit',
                                       trans('messages.limit'),
                                       array_combine($range = [10, 25, 50], $range)
                                       )
                                }}
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">@lang('messages.close')</button>
                        <a href="{{ request()->url() }}" class="btn btn-default">@lang('messages.clear')</a>
                        {{ Form::mSubmit() }}
                    </div>
                </div>
            {{ Form::close() }}
        </div>
    </div>

    <ul class="document-grid row">
        @foreach ($documents->values() as $idx => $document)
            <li class="col-md-4 col-sm-6 col-xs-12">
                @include('components/document-card', ['document' => $document])
            </li>

            @if (($idx+1) % 3 == 0)
                <div class="clearfix visible-md-block visible-lg-block"></div>
            @endif

            @if (($idx+1) % 2 == 0)
                <div class="clearfix visible-sm-block"></div>
            @endif
        @endforeach
    </ul>

    <div class="text-center">
        @include('components.pagination', ['collection' => $documents])
    </div>
@endsection

@extends('layouts.app')

@section('pageTitle', trans('messages.admin.manage_sponsors'))

@section('content')
    @include('components.breadcrumbs.admin')

    <div class="page-header">
        <button type="button" class="btn btn-default pull-right" data-toggle="modal" data-target="#queryModal" aria-label="{{trans('messages.advanced_search') }}">
            <span class="hidden-xs">@lang('messages.advanced_search')</span>
            <span class="visible-xs"><i class="fa fa-search" aria-hidden="true"></i></span>
        </button>
        <h1>{{ trans('messages.sponsor.list') }}</h1>
    </div>

    @include('components.errors')

    <div class="modal fade" id="queryModal" tabindex="-1" role="dialog" aria-labelledby="queryModalLabel">
        <div class="modal-dialog" role="document">
            {{ Form::open(['route' => 'admin.sponsors.index', 'method' => 'get']) }}
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="{{ trans('messages.close') }}"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="queryModalLabel">@lang('messages.advanced_search')</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8">
                                {{ Form::mInput('text', 'q', trans('messages.search.sponsors')) }}
                            </div>
                            <div class="col-md-4">
                                {{ Form::mSelect(
                                        'status',
                                        trans('messages.sponsor.status'),
                                        $statuses->toArray()
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
                                           'name' => trans('messages.sponsor.name'),
                                           'display_name' => trans('messages.sponsor.display_name'),
                                           'address1' => trans('messages.info.address1'),
                                           'address2' => trans('messages.info.address2'),
                                           'city' => trans('messages.info.city'),
                                           'state' => trans('messages.info.state'),
                                           'postal_code' => trans('messages.info.postal_code'),
                                           'phone' => trans('messages.info.phone'),
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


    <div class="row">
        @include('admin.partials.admin-sidebar')

        <div class="col-md-9">
            @include('sponsors.partials.table', ['sponsors' => $sponsors])
        </div>
    </div>
@endsection

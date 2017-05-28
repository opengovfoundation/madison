@extends('layouts.app')

@section('pageTitle', trans('messages.admin.manage_users'))

@section('content')
    @include('components.breadcrumbs.admin')

    <div class="page-header">
        <button type="button" class="btn btn-default pull-right" data-toggle="modal" data-target="#queryModal" aria-label="{{trans('messages.advanced_search') }}">
            <span class="hidden-xs">@lang('messages.advanced_search')</span>
            <span class="visible-xs"><i class="fa fa-search" aria-hidden="true"></i></span>
        </button>
        <h1>{{ trans('messages.admin.manage_users') }}</h1>
    </div>

    @include('components.errors')

    <div class="modal fade" id="queryModal" tabindex="-1" role="dialog" aria-labelledby="queryModalLabel">
        <div class="modal-dialog" role="document">
            {{ Form::open(['route' => 'admin.users.index', 'method' => 'get']) }}
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="{{ trans('messages.close') }}"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="queryModalLabel">@lang('messages.advanced_search')</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                {{ Form::mInput('text', 'q', trans('messages.search.users')) }}
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
                                           'fname' => trans('messages.user.fname'),
                                           'lname' => trans('messages.user.lname'),
                                           'email' => trans('messages.user.email'),
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
            <table class="table">
                <thead>
                    <tr>
                        <th>@lang('messages.user.fname')</th>
                        <th>@lang('messages.user.lname')</th>
                        <th>@lang('messages.user.email')</th>
                        <th>@lang('messages.email_verified')</th>
                        <th>@lang('messages.administrator')</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->fname }}</td>
                            <td>{{ $user->lname }}</td>
                            <td>{{ $user->email }}</td>
                            @if (empty($user->token))
                                <td class="text-center"><i class="fa fa-check"></i></td>
                            @else
                                <td></td>
                            @endif
                            <td>
                                <div class="btn-toolbar" role="toolbar">
                                    <div class="btn-group" role="group">
                                        @if ($user->isAdmin())
                                            {{ Form::open(['route' => ['admin.users.postAdmin', $user], 'method' => 'post']) }}
                                                <input type="hidden" name="admin" value="0">
                                                @if ($user->id !== Auth::user()->id)
                                                    <button type="submit" class="btn btn-danger btn-xs admin">
                                                        @lang('messages.user.remove_admin')
                                                    </button>
                                                @endif
                                            {{ Form::close() }}
                                        @else
                                            {{ Form::open(['route' => ['admin.users.postAdmin', $user], 'method' => 'post']) }}
                                                <input type="hidden" name="admin" value="1">
                                                <button type="submit" class="btn btn-default btn-xs admin">
                                                    @lang('messages.user.make_admin')
                                                </button>
                                            {{ Form::close() }}
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('users.settings.account.edit', $user) }}">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="text-center">
                @include('components.pagination', ['collection' => $users])
            </div>

        </div>
    </div>

@endsection

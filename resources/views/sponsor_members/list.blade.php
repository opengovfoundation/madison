@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1>{{ trans('messages.sponsor_member.list') }} - {{ $sponsor->display_name }}</h1>
    </div>

    @include('components.errors')

    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#queryModal">Query</button>
    {{ Html::linkRoute('sponsors.members.create', trans('messages.sponsor_member.add'), [$sponsor], ['class' => 'btn btn-default'])}}

    <div class="modal fade" id="queryModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Query</h4>
                </div>
                <div class="modal-body">
                    {{ Form::open(['route' => ['sponsors.members.index', $sponsor], 'method' => 'get']) }}
                        {{ Form::mSelect(
                               'roles[]',
                               trans('messages.sponsor_member.role'),
                               $allRoles,
                               null,
                               ['multiple' => true]
                               )
                        }}
                        {{ Form::mSelect(
                               'order',
                               trans('messages.order_by'),
                               [
                                   'created_at' => trans('messages.created'),
                                   'updated_at' => trans('messages.updated'),
                                   'role' => trans('messages.sponsor_member.role'),
                               ])
                        }}
                        {{ Form::mSelect(
                               'order_dir',
                               trans('messages.order_by_direction'),
                               [
                                   'DESC' => trans('messages.order_by_dir_desc'),
                                   'ASC' => trans('messages.order_by_dir_asc')
                               ])
                        }}
                        {{ Form::mSelect(
                               'limit',
                               trans('messages.limit'),
                               array_combine($range = [10, 25, 50], $range)
                               )
                        }}

                        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
                        <a href="{{ request()->url() }}" class="btn btn-default">@lang('messages.clear')</a>
                        {{ Form::mSubmit() }}
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>@lang('messages.sponsor.name')</th>
                <th>@lang('messages.sponsor_member.role')</th>
                <th>@lang('messages.sponsor_member.joined')</th>
                <th>@lang('messages.actions')</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($members as $member)
                <tr>
                    <td>{{ $member->name }}</td>
                    <td>{{ $member->role }}</td>
                    <td>{{ $member->created_at->toDateTimeString() }}</td>
                    <td>
                        <div class="btn-toolbar" role="toolbar">
                            @if ($sponsor->isSponsorOwner(Auth::user()->id) || Auth::user()->isAdmin())
                                <div class="btn-group">
                                    {{ Form::open(['route' => ['sponsors.members.destroy', $sponsor, $member], 'method' => 'delete']) }}
                                        <button type="submit" class="btn btn-default">{{ trans('messages.sponsor_member.remove') }}</button>
                                    {{ Form::close() }}
                                </div>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="text-center">
        @include('components.pagination', ['collection' => $members])
    </div>
@endsection

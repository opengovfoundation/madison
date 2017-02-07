@extends('layouts.app')

@if ($onlyUserSponsors)
    @section('pageTitle', trans('messages.sponsor.my_sponsors'))
@else
    @section('pageTitle', trans('messages.sponsor.list'))
@endif

@section('content')
    <div class="page-header">
        @if ($onlyUserSponsors)
            @if (request()->user()->isAdmin())
                {{ Html::linkRoute('sponsors.index', trans('messages.sponsor.all_sponsors'), ['all' => 'true'], ['class' => 'btn btn-default pull-right'])}}
            @endif
            <h1>{{ trans('messages.sponsor.my_sponsors') }}</h1>
        @else
            {{ Html::linkRoute('sponsors.index', trans('messages.sponsor.my_sponsors'), [], ['class' => 'btn btn-default pull-right'])}}
            <h1>{{ trans('messages.sponsor.list') }}</h1>
        @endif
    </div>

    @include('components.errors')

    {{ Html::linkRoute('sponsors.create', trans('messages.sponsor.create'), [], ['class' => 'btn btn-default'])}}

    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#queryModal">Query</button>

    <div class="modal fade" id="queryModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Query</h4>
                </div>
                <div class="modal-body">
                    {{ Form::open(['route' => 'sponsors.index', 'method' => 'get']) }}
                        {{ Form::mInput('text', 'name', trans('messages.sponsor.name')) }}
                        {{ Form::mSelect(
                               'statuses[]',
                               trans('messages.sponsor.status'),
                               collect($validStatuses)->mapWithKeys_v2(function ($item) {return [$item => trans('messages.sponsor.statuses.'.$item)]; })->toArray(),
                               null,
                               ['multiple' => true]
                               )
                        }}
                        {{ Form::mSelect(
                               'user_id[]',
                               trans('messages.sponsor.member'),
                               $users->mapWithKeys_v2(function ($item) {return [$item->id => $item->getDisplayName()]; })->toArray(),
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
                                   'name' => trans('messages.sponsor.name'),
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
                <th>@lang('messages.id')</th>
                <th>@lang('messages.sponsor.name')</th>
                <th>@lang('messages.created')</th>
                @if ($canSeeAtLeastOneStatus)
                    <th>@lang('messages.sponsor.status')</th>
                @endif
                <th>@lang('messages.actions')</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sponsors as $sponsor)
                <tr>
                    <td>{{ $sponsor->id }}</td>
                    <td>{{ $sponsor->name }}</td>
                    <td>{{ $sponsor->created_at->toDateTimeString() }}</td>
                    @if ($canSeeAtLeastOneStatus)
                        <td>
                            @if ($sponsorsCapabilities[$sponsor->id]['editStatus'])
                                {{ Form::open(['route' => ['sponsors.status.update', $sponsor->id], 'method' => 'put']) }}
                                    {{ Form::select(
                                        'status',
                                        collect($validStatuses)->mapWithKeys_v2(function ($item) {return [$item => trans('messages.sponsor.statuses.'.$item)]; })->toArray(),
                                        $sponsor->status,
                                        [ 'onchange' => 'if (this.selectedIndex >= 0) this.form.submit();' ]
                                        )
                                    }}
                                {{ Form::close() }}
                            @elseif ($sponsorsCapabilities[$sponsor->id]['viewStatus'])
                                {{ trans('messages.sponsor.statuses.'.$sponsor->status) }}
                            @else
                                {{-- do nothing --}}
                            @endif
                        </td>
                    @endif
                    <td>
                        <div class="btn-toolbar" role="toolbar">
                            @foreach ($sponsorsCapabilities[$sponsor->id] as $cap => $allowed)
                                @if (!$allowed)
                                    {{-- do nothing --}}
                                @elseif ($cap === 'viewDocs')
                                    <div class="btn-group" role="group">
                                        {{ Html::linkRoute(
                                                'documents.index',
                                                trans('messages.sponsor.view_docs'),
                                                ['sponsor_id' => [$sponsor->id]],
                                                ['class' => 'btn btn-default']
                                                )
                                        }}
                                    </div>
                                @elseif ($cap === 'viewMembers')
                                    <div class="btn-group" role="group">
                                        {{ Html::linkRoute(
                                                'sponsors.members.index',
                                                trans('messages.sponsor.members'),
                                                ['sponsor' => $sponsor->id],
                                                ['class' => 'btn btn-default']
                                                )
                                        }}
                                    </div>
                                @elseif ($cap === 'edit')
                                    <div class="btn-group" role="group">
                                        {{ Html::linkRoute(
                                                'sponsors.edit',
                                                trans('messages.edit'),
                                                ['sponsor' => $sponsor->id],
                                                ['class' => 'btn btn-default']
                                                )
                                        }}
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="text-center">
        @include('components.pagination', ['collection' => $sponsors])
    </div>
@endsection

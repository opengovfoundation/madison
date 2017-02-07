@extends('layouts.app')

@section('pageTitle', trans('messages.document.list'))

@section('content')
    <div class="page-header">
        <h1>{{ trans('messages.document.list') }}</h1>
    </div>

    @include('components.errors')

    @if (Auth::user())
        {{ Html::linkRoute('documents.create', trans('messages.document.create'), [], ['class' => 'btn btn-default'])}}
    @endif

    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#queryModal">Query</button>

    <div class="modal fade" id="queryModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Query</h4>
                </div>
                <div class="modal-body">
                    {{ Form::open(['route' => 'documents.index', 'method' => 'get']) }}
                        {{ Form::mInput('text', 'title', trans('messages.document.title')) }}
                        {{ Form::mSelect(
                               'sponsor_id[]',
                               trans('messages.document.sponsor'),
                               $sponsors->mapWithKeys_v2(function ($item) {return [$item->id => $item->display_name]; })->toArray(),
                               null,
                               ['multiple' => true]
                               )
                        }}
                        {{ Form::mSelect(
                               'category_id[]',
                               trans('messages.document.category'),
                               $categories->mapWithKeys_v2(function ($item) {return [$item->id => $item->name]; })->toArray(),
                               null,
                               ['multiple' => true]
                               )
                        }}
                        @if (Auth::user())
                            {{ Form::mSelect(
                                   'publish_state[]',
                                   trans('messages.document.publish_state'),
                                   collect($publishStates)->mapWithKeys_v2(function ($item) {return [$item => trans('messages.document.publish_states.'.$item)]; })->toArray(),
                                   null,
                                   ['multiple' => true]
                                   )
                            }}
                        @endif
                        {{ Form::mSelect(
                               'discussion_state[]',
                               trans('messages.document.discussion_state'),
                               collect($discussionStates)->mapWithKeys_v2(function ($item) {return [$item => trans('messages.document.discussion_states.'.$item)]; })->toArray(),
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
                                   'title' => trans('messages.document.title'),
                                   'activity' => trans('messages.document.activity')
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
                <th>@lang('messages.document.title')</th>
                <th>@lang('messages.created')</th>
                <th>@lang('messages.document.sponsor')</th>
                @if (Auth::user() && Auth::user()->isAdmin())
                    <th>@lang('messages.document.publish_state')</th>
                @endif
                <th>@lang('messages.actions')</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($documents as $document)
                <tr>
                    <td>{{ $document->id }}</td>
                    <td>{{ $document->title }}</td>
                    <td>{{ $document->created_at->toDateTimeString() }}</td>
                    <td>{{ $document->sponsors->shift()->display_name }}
                        @if ($document->sponsors->count() > 1)
                            @lang('messages.document.sponsor_others')
                        @endif
                    </td>
                    @if (Auth::user() && Auth::user()->isAdmin())
                        <td>{{ trans('messages.document.publish_states.'.$document->publish_state) }}</td>
                    @endif
                    <td>
                        <div class="btn-toolbar" role="toolbar">
                            @foreach ($documentsCapabilities[$document->id] as $cap => $allowed)
                                @if (!$allowed)
                                    {{-- do nothing --}}
                                @elseif ($cap === 'open')
                                    <div class="btn-group" role="group">
                                        {{ Html::linkRoute(
                                                'documents.show',
                                                trans('messages.open'),
                                                ['document' => $document->slug],
                                                ['class' => 'btn btn-default']
                                                )
                                        }}
                                    </div>
                                @elseif ($cap === 'edit')
                                    <div class="btn-group" role="group">
                                        {{ Html::linkRoute(
                                                'documents.edit',
                                                trans('messages.edit'),
                                                ['document' => $document->slug],
                                                ['class' => 'btn btn-default']
                                                )
                                        }}
                                    </div>
                                @elseif ($cap === 'delete')
                                    <div class="btn-group" role="group">
                                        {{ Form::open(['route' => ['documents.destroy', $document], 'method' => 'delete']) }}
                                            <button type="submit" class="btn btn-default">{{ trans('messages.delete') }}</button>
                                        {{ Form::close() }}
                                    </div>
                                @elseif ($cap === 'restore')
                                    <div class="btn-group" role="group">
                                        {{ Html::linkRoute(
                                                'documents.restore',
                                                trans('messages.restore'),
                                                ['document' => $document->slug],
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
        @include('components.pagination', ['collection' => $documents])
    </div>
@endsection

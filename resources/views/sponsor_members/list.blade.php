@extends('layouts.app')

@section('pageTitle', trans('messages.sponsor.page_title_members', ['sponsorName' => $sponsor->display_name]))

@section('content')
    <div class="page-header">
        <h1>{{ $sponsor->display_name }}</h1>
        @include('components.breadcrumbs.sponsor', ['sponsor' => $sponsor])
    </div>

    @include('components.errors')

    <div class="row">
        @include('sponsors.partials.sponsor-sidebar', ['sponsor' => $sponsor])
        <div class="col-md-9">
            <table class="table">
                <thead>
                    <tr>
                        <th>@lang('messages.sponsor.name')</th>
                        <th>@lang('messages.user.email')</th>
                        <th>@lang('messages.sponsor_member.role')</th>
                        <th>@lang('messages.sponsor_member.joined')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($members as $member)
                        <tr>
                            <td>{{ $member->name }}</td>
                            <td>{{ $member->email }}</td>
                            <td>
                                @if ($sponsor->isSponsorOwner(Auth::user()->id) || Auth::user()->isAdmin())
                                    {{ Form::open(['route' => ['sponsors.members.role.update', $sponsor, $member], 'method' => 'put']) }}
                                        {{ Form::select(
                                            'role',
                                            $allRoles,
                                            $member->role,
                                            [ 'onchange' => 'if (this.selectedIndex >= 0) this.form.submit();' ]
                                            )
                                        }}
                                    {{ Form::close() }}
                                @else
                                    {{ trans('messages.sponsor_member.roles.'.$member->role) }}
                                @endif
                            </td>
                            <td>
                                @include('components/date', [
                                'datetime' => $member->created_at,
                                ])
                            </td>
                            <td>
                                <div class="btn-toolbar" role="toolbar">
                                    @if ($sponsor->isSponsorOwner(Auth::user()->id) || Auth::user()->isAdmin())
                                        <div class="btn-group">
                                            {{ Form::open(['route' => ['sponsors.members.destroy', $sponsor, $member], 'method' => 'delete']) }}
                                                <button type="submit" class="btn btn-xs btn-link">
                                                    <i class="fa fa-close"></i>
                                                </button>
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

            <hr>

            {{ Html::linkRoute('sponsors.members.create', trans('messages.sponsor_member.add'), [$sponsor], ['class' => 'btn btn-primary'])}}
        </div>
    </div>
@endsection

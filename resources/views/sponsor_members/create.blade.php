@extends('layouts.app')

@section('pageTitle', trans('messages.sponsor_member.create'))

@section('content')
    <div class="page-header">
        <h1>{{ trans('messages.sponsor_member.create') }}</h1>
        @include('components.breadcrumbs.sponsor', ['sponsor' => $sponsor])
    </div>

    @include('components.errors')

    <div class="row">
        @include('sponsors.partials.sponsor-sidebar')
        <div class="col-md-9">
            {{ Form::open(['route' => ['sponsors.members.store', $sponsor]]) }}
                {{ Form::mInput('text', 'email', trans('messages.user.email')) }}
                {{ Form::mSelect('role', trans('messages.sponsor_member.role'), $allRoles, null) }}
                {{ Form::mSubmit(trans('messages.sponsor_member.add_user')) }}
            {{ Form::close() }}
        </div>
    </div>
@endsection

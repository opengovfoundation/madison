@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1>{{ trans('messages.sponsor_member.create') }}</h1>
    </div>

    @include('components.errors')

    {{ Form::open(['route' => ['sponsors.members.store', $sponsor]]) }}
        {{ Form::mInput('text', 'email', trans('messages.user.email')) }}
        {{ Form::mSelect('role', trans('messages.sponsor_member.role'), $allRoles, null) }}
        {{ Form::mSubmit() }}
    {{ Form::close() }}
@endsection

@extends('users.settings')

@section('pageTitle', trans('messages.user.settings_pages.password'))

@section('settings_content')
    {{ Form::open(['route' => ['users.settings.password.update', $user->id], 'method' => 'put']) }}
        {{ Form::mInput('password', 'new_password', trans('messages.user.new_password')) }}
        {{ Form::mInput('password', 'new_password_confirmation', trans('messages.user.new_password_confirmation')) }}
        <hr>
        {{ Form::mSubmit(trans('messages.save')) }}
    {{ Form::close() }}
@endsection

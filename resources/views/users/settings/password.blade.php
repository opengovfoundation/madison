@extends('users.settings')

@section('pageTitle', trans('messages.user.settings_pages.password'))

@section('settings_content')
    {{ Form::open(['route' => ['users.settings.password.update', $user->id], 'method' => 'put']) }}
        <div class="row">
            <div class="col-md-6">
                {{ Form::mInput('password', 'new_password', trans('messages.user.new_password')) }}
            </div>
            <div class="col-md-6">
                {{ Form::mInput('password', 'new_password_confirmation', trans('messages.user.new_password_confirmation')) }}
            </div>
        </div>
        <hr>
        {{ Form::mSubmit() }}
    {{ Form::close() }}
@endsection

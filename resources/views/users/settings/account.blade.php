@extends('users.settings')

@section('pageTitle', trans('messages.user.settings_pages.account'))

@section('settings_content')
    {{ Form::model($user, ['route' => ['users.settings.account.update', $user->id], 'method' => 'put']) }}
        {{ Form::mInput('text', 'fname', trans('messages.user.fname'), null, ['required' => '']) }}
        {{ Form::mInput('text', 'lname', trans('messages.user.lname'), null, ['required' => '']) }}
        {{ Form::mInput('email', 'email', trans('messages.user.email'), null, ['required' => ''], trans('messages.user.email_help')) }}
        <hr>
        {{ Form::mSubmit(trans('messages.save')) }}
    {{ Form::close() }}
@endsection

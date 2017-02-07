@extends('users.settings')

@section('pageTitle', trans('messages.user.settings_pages.notifications'))

@section('settings_content')
    {{ Form::open(['route' => ['users.settings.notifications.update', $user->id], 'method' => 'put']) }}
        @foreach($notificationPreferences as $notificationClass => $value)
            {{ Form::mInput(
                   'checkbox',
                   $notificationClass::getName(),
                   trans($notificationClass::baseMessageLocation().'.preference_description'),
                   $value
            ) }}
        @endforeach
        <hr>
        {{ Form::mSubmit() }}
    {{ Form::close() }}
@endsection

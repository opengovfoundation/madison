@extends('users.settings')

@section('settings_content')
    {{ Form::open(['route' => ['users.settings.notifications.update', $user->id], 'method' => 'put']) }}
        @foreach($notificationPreferences as $notificationName => $value)
            {{ Form::mInput('checkbox', $notificationName, trans('messages.notifications.descriptions.'.$notificationName), $value) }}
        @endforeach
        <hr>
        {{ Form::mSubmit() }}
    {{ Form::close() }}
@endsection

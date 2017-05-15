@extends('users.settings')

@section('pageTitle', trans('messages.user.settings_pages.notifications'))

@section('settings_content')
    {{ Form::open(['route' => ['users.settings.notifications.update', $user->id], 'method' => 'put']) }}
        @foreach($notificationPreferenceGroups as $group => $notificationPreferences)
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">@lang('messages.notifications.groups.'.$group)</h3>
                </div>
                <div class="panel-body">
                    @foreach($notificationPreferences as $notificationClass => $value)
                        @php($targetNotification = request()->input('notification') === $notificationClass::getName())
                        <div class="{{ $targetNotification ? 'anchor-target' : '' }}">
                            {{ Form::mInput(
                                'checkbox',
                                $notificationClass::getName(),
                                trans($notificationClass::baseMessageLocation().'.preference_description'),
                                $value
                            ) }}
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
        <hr>
        {{ Form::mSubmit(trans('messages.save')) }}
    {{ Form::close() }}
@endsection

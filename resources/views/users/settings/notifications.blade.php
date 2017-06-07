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
                        <div class="form-horizontal {{ $targetNotification ? 'anchor-target' : '' }}">
                            <div class="form-group">
                                <label for="{{ $notificationClass::getName() }}" class="col-xs-12 col-md-9 control-label">
                                   @lang($notificationClass::baseMessageLocation().'.preference_description')
                                </label>
                                <div class="col-xs-12 col-md-3">
                                    {{ Form::select(
                                        $notificationClass::getName(),
                                        collect($frequencyOptions)->mapWithKeys_v2(function ($f) { return [$f => trans('messages.notifications.frequencies.'.$f.'.label')]; })->toArray(),
                                        $value,
                                        [
                                            'id' => $notificationClass::getName(),
                                            'class' => 'no-select2 form-control',
                                            'autocomplete' => 'off',
                                        ]
                                    ) }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
        <hr>
        {{ Form::mSubmit(trans('messages.save')) }}
    {{ Form::close() }}
@endsection

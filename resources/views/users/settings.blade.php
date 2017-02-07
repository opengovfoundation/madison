@extends('layouts.app')

@section('content')
    {{-- TODO: set this differently for each page? --}}
    <div class="page-header">
        <h1>{{ trans('messages.settings') }}: {{ $user->displayName }}</h1>
    </div>

    @include('components.errors')

    <div class="col-md-3 list-group">
        @php ($settingsPages = ['account', 'password', 'notifications'])
        @foreach($settingsPages as $setting)
            @php ($settingUrl = route('users.settings.'.$setting.'.edit', $user->id))
            <a href="{{ $settingUrl }}" class="list-group-item {{request()->url() === $settingUrl ? 'active' : ''}}">
                @lang('messages.user.settings_pages.'.$setting)
            </a>
        @endforeach
    </div>

    <div class="col-md-9">
        @yield('settings_content')
    </div>
@endsection

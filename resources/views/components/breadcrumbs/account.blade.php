<ol class="breadcrumb small">
    <li>
        <a href="{{ route('users.settings.edit', Auth::user()) }}">
            @lang('messages.user.settings_pages.account')
        </a>
    </li>
    @if (Route::currentRouteName() === 'sponsors.create')
        <li>
            <a href="{{ route('users.sponsors.index', Auth::user()) }}">
                @lang('messages.sponsor.list')
            </a>
        </li>
    @endif
    <li class="active">
        @if (Route::currentRouteName() === 'sponsors.create')
            @lang('messages.new')
        @elseif (Route::currentRouteName() === 'users.sponsors.index')
            @lang('messages.sponsor.list')
        @elseif (Route::currentRouteName() === 'users.settings.account.edit')
            @lang('messages.settings')
        @elseif (Route::currentRouteName() === 'users.settings.password.edit')
            @lang('messages.user.settings_pages.password')
        @elseif (Route::currentRouteName() === 'users.settings.notifications.edit')
            @lang('messages.user.settings_pages.notifications')
        @endif
    </li>
</ol>

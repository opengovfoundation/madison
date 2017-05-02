<ol class="breadcrumb small">
    <li>
        <a href="{{ route('users.settings.edit', Auth::user()) }}">
            @lang('messages.user.settings_pages.account')
        </a>
    </li>
    <li>
        <a href="{{ route('users.sponsors.index', Auth::user()) }}">
            @lang('messages.sponsor.list')
        </a>
    </li>
    <li>
        <a href="{{ route('sponsors.documents.index', $sponsor) }}">
            {{ $sponsor->display_name }}
        </a>
    </li>
    @if (Route::currentRouteName() === 'sponsors.members.create')
        <li>
            <a href="{{ route('sponsors.members.index', $sponsor) }}">
                @lang('messages.sponsor.members')
            </a>
        </li>
    @endif

    <li class="active">
        @if (Route::currentRouteName() === 'sponsors.documents.index')
            @lang('messages.document.list')
        @elseif (Route::currentRouteName() === 'sponsors.members.create')
            @lang('messages.add')
        @elseif (Route::currentRouteName() === 'sponsors.members.index')
            @lang('messages.sponsor.members')
        @elseif (Route::currentRouteName() === 'sponsors.edit')
            @lang('messages.settings')
        @endif
    </li>
</ol>

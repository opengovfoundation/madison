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
    <li>
        <a href="{{ route('sponsors.documents.index', $sponsor) }}">
            @lang('messages.document.list')
        </a>
    </li>
    <li>
        <a href="{{ route('documents.manage.settings', $document) }}">
            {{ $document->title }}
        </a>
    </li>

    <li class="active">
        @if (Route::currentRouteName() === 'documents.manage.settings')
            @lang('messages.settings')
        @elseif (Route::currentRouteName() === 'documents.manage.comments')
            @lang('messages.document.comments')
        @endif
    </li>
</ol>

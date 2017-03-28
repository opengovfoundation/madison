<ol class="breadcrumb small">
    <li>
        <a href="{{ route('admin.site.index') }}">
            @lang('messages.administrator')
        </a>
    </li>
    <li class="active">
        @if (Route::currentRouteName() === 'admin.site.index')
            @lang('messages.settings')
        @elseif (Route::currentRouteName() === 'admin.pages.index')
            @lang('messages.admin.pages')
        @elseif (Route::currentRouteName() === 'admin.featured-documents.index')
            @lang('messages.admin.featured_documents')
        @elseif (Route::currentRouteName() === 'admin.users.index')
            @lang('messages.admin.manage_users')
        @elseif (Route::currentRouteName() === 'admin.sponsors.index')
            @lang('messages.admin.manage_sponsors')
        @endif
    </li>
</ol>

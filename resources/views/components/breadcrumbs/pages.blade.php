<ol class="breadcrumb small">
    <li>
        <a href="{{ route('admin.site.index') }}">
            @lang('messages.administrator')
        </a>
    </li>
    <li>
        <a href="{{ route('admin.pages.index') }}">
            @lang('messages.admin.pages')
        </a>
    </li>
    <li class="active">{{ $page->nav_title }}</li>
</ol>

<div class="col-md-3">
    <div class="list-group">
        <a href="{{ route('admin.site.index') }}"
            class="list-group-item {{ active(['admin.site.index']) }}">

            @lang('messages.admin.site_settings')
        </a>
        <a href="{{ route('admin.pages.index') }}"
            class="list-group-item {{ active(['admin.pages.index']) }}">

            @lang('messages.admin.custom_pages')
        </a>
        <a href="{{ route('admin.featured-documents.index') }}"
            class="list-group-item {{ active(['admin.featured-documents.index']) }}">

            @lang('messages.admin.featured_documents')
        </a>
        <a href="{{ route('admin.users.index') }}"
            class="list-group-item {{ active(['admin.users.index']) }}">

            @lang('messages.admin.manage_users')
        </a>
        <a href="{{ route('admin.sponsors.index') }}"
            class="list-group-item {{ active(['admin.sponsors.index']) }}">

            @lang('messages.admin.manage_sponsors')
        </a>
    </div>
</div>

<div class="col-md-3">
    <div class="list-group">
        <a href="{{ route('admin.site.index') }}"
            class="list-group-item {{ Request::route()->getName() !== 'admin.site.index' ?: 'active' }}">

            @lang('messages.admin.site_settings')
        </a>
        <a href="{{ route('admin.pages.index') }}"
            class="list-group-item {{ Request::route()->getName() !== 'admin.pages.index' ?: 'active' }}">

            @lang('messages.admin.custom_pages')
        </a>
        <a href="{{ route('admin.featured-documents.index') }}"
            class="list-group-item {{ Request::route()->getName() !== 'admin.featured-documents.index' ?: 'active' }}">

            @lang('messages.admin.featured_documents')
        </a>
        <a href="{{ route('admin.users.index') }}"
            class="list-group-item {{ Request::route()->getName() !== 'admin.users.index' ?: 'active' }}">

            @lang('messages.admin.manage_users')
        </a>
    </div>
</div>

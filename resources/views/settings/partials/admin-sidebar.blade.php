<div class="col-md-3">
    <div class="list-group">
        <a href="{{ route('settings.site.index') }}"
            class="list-group-item {{ Request::route()->getName() !== 'settings.site.index' ?: 'active' }}">

            @lang('messages.setting.site_settings')
        </a>
        <a href="{{ route('settings.pages.index') }}"
            class="list-group-item {{ Request::route()->getName() !== 'settings.pages.index' ?: 'active' }}">

            @lang('messages.setting.custom_pages')
        </a>
        <a href="{{ route('settings.featured-documents.index') }}"
            class="list-group-item {{ Request::route()->getName() !== 'settings.featured-documents.index' ?: 'active' }}">

            @lang('messages.setting.featured_documents')
        </a>
    </div>
</div>

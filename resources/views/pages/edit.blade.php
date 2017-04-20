@extends('layouts.app')

@section('pageTitle', trans('messages.page.edit'))

@section('content')
    @include('components.breadcrumbs.pages', ['page' => $page])

    <div class="page-header">
        <h1>{{ trans('messages.page.edit') }}</h1>
    </div>

    @include('components.errors')

    <div class="row">
        @include('admin.partials.admin-sidebar')
        <div class="col-md-9">
            {{ Form::model($page, ['route' => ['pages.update', $page->id], 'method' => 'put']) }}
                {{ Form::mInput('text', 'url', trans('messages.page.url')) }}
                {{ Form::mInput('text', 'nav_title', trans('messages.page.nav_title')) }}
                {{ Form::mInput('text', 'page_title', trans('messages.page.page_title')) }}
                {{ Form::mInput('text', 'header', trans('messages.page.header')) }}

                {{ Form::mInput('checkbox', 'header_nav_link', trans('messages.page.show_in_header'), $page->header_nav_link) }}
                {{ Form::mInput('checkbox', 'footer_nav_link', trans('messages.page.show_in_footer'), $page->footer_nav_link) }}
                {{ Form::mInput('checkbox', 'external', trans('messages.page.external'), $page->external) }}

                {{ Form::mInput('textarea', 'page_content', trans('messages.document.content'), $pageContent) }}

                {{ Form::mSubmit() }}
            {{ Form::close() }}
        </div>
    </div>

    @push('scripts')
        <script>
            // if external is checked, hide page title, page_header, and content
            let $external = $('input[type="checkbox"][name="external"]');

            let $pageTitle = $('input[name="page_title"]').parent();
            let $header = $('input[name="header"]').parent();
            let $content = $('textarea[name="page_content"]').parent();

            hideOrShowInternalFields();
            $external.change(hideOrShowInternalFields);

            function hideOrShowInternalFields() {
                if ($external.prop('checked')) {
                    $pageTitle.hide();
                    $header.hide();
                    $content.hide();
                } else {
                    $pageTitle.show();
                    $header.show();
                    $content.show();
                }
            }
        </script>
    @endpush

@endsection

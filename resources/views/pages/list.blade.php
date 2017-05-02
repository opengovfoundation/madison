@extends('layouts.app')

@section('pageTitle', trans('messages.admin.custom_pages'))

@section('content')
    @include('components.breadcrumbs.admin')

    <div class="page-header">
        <h1>{{ trans('messages.admin.admin_label', ['page' => trans('messages.admin.custom_pages')]) }}</h1>
    </div>

    @include('components.errors')


    <div class="row">
        @include('admin.partials.admin-sidebar')

        <div class="col-md-9">
            <table class="table">
                <thead>
                    <tr>
                        <th>@lang('messages.page.title')</th>
                        <th>@lang('messages.page.url')</th>
                        <th>@lang('messages.page.show_in_header')</th>
                        <th>@lang('messages.page.show_in_footer')</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pages as $page)
                        <tr>
                            <td>
                                <a href="{{ $page->getUrl() }}" title="{{ $page->title }}">
                                    {{ $page->nav_title }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ $page->getUrl() }}" title="{{ $page->title }}">
                                    {{ $page->url }}
                                </a>
                            </td>
                            <td class="text-center">{!! $page->header_nav_link ? '<i class="fa fa-check"></i>' : '<i class="fa fa"></i>' !!}</td>
                            <td class="text-center">{!! $page->footer_nav_link ? '<i class="fa fa-check"></i>' : '<i class="fa fa"></i>' !!}</td>
                            <td>
                                <a href="{{ route('pages.edit', $page) }}"
                                    title="@lang('messages.page.edit')"
                                    class="btn btn-xs btn-link edit">

                                    <i class="fa fa-pencil"></i>
                                </a>

                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    {{ Form::open(['route' => ['pages.destroy', $page], 'method' => 'delete']) }}
                                        <button type="submit" class="btn btn-xs btn-link destroy">
                                            <i class="fa fa-close"></i>
                                        </button>
                                    {{ Form::close() }}
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ Html::linkRoute('pages.create', trans('messages.page.create'), [], ['class' => 'btn btn-default new'])}}
        </div>
    </div>

@endsection

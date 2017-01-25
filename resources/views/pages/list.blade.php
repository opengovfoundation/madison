@extends('layouts.app')

@section('content')

    <div class="page-header">
        <h1>{{ trans('messages.page.manage') }}</h1>
    </div>

    @include('components.errors')

    {{ Html::linkRoute('pages.create', trans('messages.page.create'), [], ['class' => 'btn btn-default'])}}

    <table class="table">
        <thead>
            <tr>
                <th>@lang('messages.page.nav_title')</th>
                <th>@lang('messages.page.url')</th>
                <th>@lang('messages.page.show_in_header')</th>
                <th>@lang('messages.page.show_in_footer')</th>
                <th>@lang('messages.actions')</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pages as $page)
                <tr>
                    <td>{{ $page->nav_title }}</td>
                    <td>{{ $page->url }}</td>
                    <td>{{ $page->header_nav_link ? "X" : "" }}</td>
                    <td>{{ $page->footer_nav_link ? "X" : "" }}</td>
                    <td>
                        {{ Html::linkRoute('pages.edit', trans('messages.edit'), [$page], ['class' => 'btn btn-default'])}}
                        <div class="btn-group" role="group">
                            {{ Form::open(['route' => ['pages.destroy', $page], 'method' => 'delete']) }}
                                <button type="submit" class="btn btn-default">{{ trans('messages.delete') }}</button>
                            {{ Form::close() }}
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

@endsection

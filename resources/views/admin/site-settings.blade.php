@extends('layouts.app')

@section('pageTitle', trans('messages.admin.site_settings'))

@section('content')
    <div class="page-header">
        <h1>{{ trans('messages.admin.admin_label', ['page' => trans('messages.settings')]) }}</h1>
        @include('components.breadcrumbs.admin')
    </div>

    @include('components.errors')

    <div class="row">
        @include('admin.partials.admin-sidebar')

        <div class="col-md-9">
            {{ Form::model($currentSettings, ['route' => ['admin.site.update'], 'method' => 'put']) }}
                @foreach ($allSettingsDesc as $key => $desc)
                    @if ($desc['type'] === 'select')
                        {{ Form::mSelect(
                                $key,
                                trans('messages.admin.'.$key),
                                $options[$key]['choices'],
                                null,
                                [],
                                trans('messages.admin.'.$key.'_help') !== 'messages.admin.'.$key.'_help'
                                       ? trans('messages.admin.'.$key.'_help')
                                       : null
                                )
                        }}
                    @elseif ($desc['type'] === 'text')
                        {{ Form::mInput(
                                'text',
                                $key,
                                trans('messages.admin.'.$key),
                                null,
                                [ 'placeholder' =>
                                    !empty($options[$key]['placeholder'])
                                        ? $options[$key]['placeholder']
                                        : ''
                                ],
                                trans('messages.admin.'.$key.'_help') !== 'messages.admin.'.$key.'_help'
                                       ? trans('messages.admin.'.$key.'_help')
                                       : null
                                )
                        }}
                    @endif
                @endforeach

                {{ Form::mSubmit() }}
            {{ Form::close() }}
        </div>
    </div>
@endsection

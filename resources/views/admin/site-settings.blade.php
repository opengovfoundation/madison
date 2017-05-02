@extends('layouts.app')

@section('pageTitle', trans('messages.admin.site_settings'))

@section('content')
    @include('components.breadcrumbs.admin')

    <div class="page-header">
        <h1>{{ trans('messages.admin.admin_label', ['page' => trans('messages.settings')]) }}</h1>
    </div>

    @include('components.errors')

    <div class="row">
        @include('admin.partials.admin-sidebar')

        <div class="col-md-9">
            {{ Form::model($currentSettings, ['route' => ['admin.site.update'], 'method' => 'put']) }}
                @foreach ($groupedSettingsDesc as $groupName => $settingsGroup)
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">@lang('messages.admin.setting_groups.' . $groupName)</h3>
                        </div>
                        <div class="panel-body">
                            @foreach ($settingsGroup as $setting)
                                @if ($setting['type'] === 'select')
                                    {{ Form::mSelect(
                                            $setting['key'],
                                            trans('messages.admin.'.$setting['key']),
                                            $options[$setting['key']]['choices'],
                                            null,
                                            [],
                                            trans('messages.admin.'.$setting['key'].'_help') !== 'messages.admin.'.$setting['key'].'_help'
                                                   ? trans('messages.admin.'.$setting['key'].'_help')
                                                   : null
                                            )
                                    }}
                                @elseif ($setting['type'] === 'text')
                                    {{ Form::mInput(
                                            'text',
                                            $setting['key'],
                                            trans('messages.admin.'.$setting['key']),
                                            null,
                                            [ 'placeholder' =>
                                                !empty($options[$setting['key']]['placeholder'])
                                                    ? $options[$setting['key']]['placeholder']
                                                    : ''
                                            ],
                                            trans('messages.admin.'.$setting['key'].'_help') !== 'messages.admin.'.$setting['key'].'_help'
                                                   ? trans('messages.admin.'.$setting['key'].'_help')
                                                   : null
                                            )
                                    }}
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach

                {{ Form::mSubmit() }}
            {{ Form::close() }}
        </div>
    </div>
@endsection

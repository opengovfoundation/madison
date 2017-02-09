@extends('layouts.app')

@section('pageTitle', trans('messages.setting.site_settings'))

@section('content')
    <div class="page-header">
        <h1>{{ trans('messages.setting.admin_label', ['page' => trans('messages.settings')]) }}</h1>
    </div>

    @include('components.errors')

    <div class="row">
        @include('settings.partials.admin-sidebar')

        <div class="col-md-9">
            {{ Form::model($currentSettings, ['route' => ['settings.site.update'], 'method' => 'put']) }}
                @foreach ($allSettingsDesc as $key => $desc)
                    @if ($desc['type'] === 'select')
                        {{ Form::mSelect(
                                $key,
                                trans('messages.setting.'.$key),
                                $options[$key]['choices'],
                                null,
                                [],
                                trans('messages.setting.'.$key.'_help') !== 'messages.setting.'.$key.'_help'
                                       ? trans('messages.setting.'.$key.'_help')
                                       : null
                                )
                        }}
                    @elseif ($desc['type'] === 'text')
                        {{ Form::mInput(
                                'text',
                                $key,
                                trans('messages.setting.'.$key),
                                null,
                                [ 'placeholder' =>
                                    !empty($options[$key]['placeholder'])
                                        ? $options[$key]['placeholder']
                                        : ''
                                ],
                                trans('messages.setting.'.$key.'_help') !== 'messages.setting.'.$key.'_help'
                                       ? trans('messages.setting.'.$key.'_help')
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

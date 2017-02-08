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
                {{ Form::mSelect(
                        'madison.date_format',
                        trans('messages.setting.date_format'),
                        $dateFormats
                        )
                }}

                {{ Form::mSelect(
                        'madison.time_format',
                        trans('messages.setting.time_format'),
                        $timeFormats
                        )
                }}

                {{ Form::mSubmit() }}
            {{ Form::close() }}
        </div>
    </div>
@endsection

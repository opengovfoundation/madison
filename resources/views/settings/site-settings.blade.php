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
            <!-- TODO -->
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('pageTitle', trans('messages.sponsor.list'))

@section('content')
    <div class="page-header">
        <h1>{{ trans('messages.sponsor.list') }}</h1>
        @include('components.breadcrumbs.account')
    </div>

    @include('components.errors')

    <div class="row">
        <div class="col-md-9">
            @include('sponsors.partials.table', ['sponsors' => $sponsors])
        </div>
        <div class="col-md-3">
            <div class="panel panel-default">
                <div class="panel-body">
                    <h4><small>@lang('messages.sponsor.create_another_header')</small></h4>
                    <p><small>@lang('messages.sponsor.create_another_body')</small></p>
                    {{ Html::linkRoute('sponsors.create', trans('messages.sponsor.create_another'), [], ['class' => 'btn btn-default btn-xs'])}}
                </div>
            </div>
        </div>
    </div>
@endsection

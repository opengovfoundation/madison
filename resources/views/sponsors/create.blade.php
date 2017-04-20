@extends('layouts.app')

@section('pageTitle', trans('messages.sponsor.create'))

@section('content')
    @include('components.breadcrumbs.account')

    <div class="page-header">
        <h1>{{ trans('messages.sponsor.create') }}</h1>
    </div>

    @include('components.errors')

    <div class="row">
        <div class="col-md-6">
            <p class="lead">@lang('messages.sponsor.create_help.what_is_a_sponsor')</p>
            <p>@lang('messages.sponsor.create_help.next_steps')</p>
            <p><a href="{{ route('sponsors.info') }}">@lang('messages.learn_more')</a></p>
        </div>
        <div class="col-md-6">
            {{ Form::open(['route' => ['sponsors.store']]) }}
                @include('sponsors.partials.form')
            {{ Form::close() }}
        </div>
    </div>
@endsection

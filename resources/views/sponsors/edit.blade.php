@extends('layouts.app')

@section('pageTitle', trans('messages.sponsor.page_title_settings', ['sponsorName' => $sponsor->display_name]))

@section('content')
    <div class="page-header">
        <h1>{{ $sponsor->display_name }}</h1>
        @include('components.breadcrumbs.sponsor', ['sponsor' => $sponsor])
    </div>

    @include('components.errors')

    <div class="row">
        @include('sponsors.partials.sponsor-sidebar', ['sponsor' => $sponsor])

        <div class="col-md-9">
            {{ Form::model($sponsor, ['route' => ['sponsors.update', $sponsor->id], 'method' => 'put']) }}
                @include('sponsors.partials.form', ['sponsor' => $sponsor])
            {{ Form::close() }}
        </div>
    </div>
@endsection

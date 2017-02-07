@extends('layouts.app')

@section('pageTitle', trans('messages.sponsor.edit'))

@section('content')
    <div class="page-header">
        <h1>{{ trans('messages.sponsor.edit') }}: {{ $sponsor->display_name }}</h1>
    </div>

    @include('components.errors')

    {{ Form::model($sponsor, ['route' => ['sponsors.update', $sponsor->id], 'method' => 'put']) }}
        @include('sponsors.partials.form', ['sponsor' => $sponsor])
    {{ Form::close() }}
@endsection

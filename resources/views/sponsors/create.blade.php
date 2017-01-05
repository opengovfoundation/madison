@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1>{{ trans('messages.sponsor.create') }}</h1>
    </div>

    @include('components.errors')

    {{ Form::open(['route' => ['sponsors.store']]) }}
        @include('sponsors.partials.form')
    {{ Form::close() }}
@endsection

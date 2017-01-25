@extends('layouts.app')

@section('content')

    <div class="page-header">
        <h1>{{ trans('messages.page.create') }}</h1>
    </div>

    @include('components.errors')

    {{ Form::open(['route' => ['pages.store']]) }}
        {{ Form::mInput('text', 'nav_title', trans('messages.page.title')) }}
        {{ Form::mSubmit() }}
    {{ Form::close() }}
@endsection

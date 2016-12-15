@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1>{{ trans('messages.document.edit') }}</h1>
    </div>

    @include('components.errors')

    {{ Form::model($document, ['route' => ['documents.update', $document->slug], 'method' => 'put']) }}
        {{ Form::mInput('text', 'title', trans('messages.document.title')) }}
        {{ Form::mSubmit() }}
    {{ Form::close() }}
@endsection

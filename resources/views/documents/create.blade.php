@extends('layouts.app')

@section('pageTitle', trans('messages.document.create'))

@section('content')
    <div class="page-header">
        <h1>{{ trans('messages.document.create') }}</h1>
    </div>

    @include('components.errors')

    {{ Form::open(['route' => ['documents.store']]) }}
        {{ Form::mInput('text', 'title', trans('messages.document.title')) }}
        {{ Form::mSelect(
                'sponsor_id',
                trans('messages.document.create_as'),
                $availableSponsors->mapWithKeys_v2(function ($item) {return [$item->id => $item->display_name]; }),
                $activeSponsor ? $activeSponsor->id : null
                )
        }}
        {{ Form::mSubmit() }}
    {{ Form::close() }}
@endsection

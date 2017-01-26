@extends('layouts.app')

@section('content')

    <div class="page-header">
        <h2>{{ $page->header }}</h1>
    </div>

    @include('components.errors')

    {!! $page->content->html() !!}
@endsection

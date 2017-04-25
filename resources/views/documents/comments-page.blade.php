@extends('layouts.app')

@section('pageTitle', 'Comment')

@section('content')
    @include('components.errors')

    @include('documents.partials.comments', ['view' => 'card', 'comments' => $comments])
@endsection

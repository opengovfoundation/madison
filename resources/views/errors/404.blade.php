@extends('layouts.app')

@section('pageTitle', trans('messages.error.404.title'))

@section('content')
    @include('errors.partials.error_content', ['status' => '404'])
@endsection

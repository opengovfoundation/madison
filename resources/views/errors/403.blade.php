@extends('layouts.app')

@section('pageTitle', trans('messages.error.403.title'))

@section('content')
    @include('errors.partials.error_content', ['status' => '403'])
@endsection

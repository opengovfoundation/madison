@extends('layouts.app')

@section('pageTitle', trans('messages.error.500.title'))

@section('content')
    @include('errors.partials.error_content', ['status' => '500'])
@endsection

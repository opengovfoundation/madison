@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1>{{ trans('messages.user.edit') }}: {{ $user->displayName }}</h1>
    </div>

    @include('components.errors')

    {{ Form::model($user, ['route' => ['users.update', $user->id], 'method' => 'put']) }}
        <div class="row">
            <div class="col-md-3">
                {{ Form::mInput('text', 'fname', trans('messages.user.fname')) }}
            </div>
            <div class="col-md-3">
                {{ Form::mInput('text', 'lname', trans('messages.user.lname')) }}
            </div>
            <div class="col-md-6">
                {{ Form::mInput('text', 'email', trans('messages.user.email')) }}
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-6">
                        {{ Form::mInput('text', 'address1', trans('messages.info.address1')) }}
                    </div>
                    <div class="col-md-6">
                        {{ Form::mInput('text', 'address2', trans('messages.info.address2')) }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        {{ Form::mInput('text', 'city', trans('messages.info.city')) }}
                    </div>
                    <div class="col-md-4">
                        {{ Form::mInput('text', 'state', trans('messages.info.state')) }}
                    </div>
                    <div class="col-md-4">
                        {{ Form::mInput('text', 'postal_code', trans('messages.info.postal_code')) }}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                {{ Form::mInput('text', 'phone', trans('messages.info.phone')) }}
                {{ Form::mInput('text', 'url', trans('messages.info.url')) }}
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-6">
                {{ Form::mInput('password', 'new_password', trans('messages.user.new_password')) }}
            </div>
            <div class="col-md-6">
                {{ Form::mInput('password', 'new_password_confirmation', trans('messages.user.new_password_confirmation')) }}
            </div>
        </div>
        <hr>
        {{ Form::mSubmit() }}
    {{ Form::close() }}
@endsection

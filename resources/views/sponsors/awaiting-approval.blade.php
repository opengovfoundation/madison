@extends('layouts.app')

@section('pageTitle', trans('messages.sponsor.waiting_approval.page_title'))

@section('content')
    <h1>@lang('messages.sponsor.waiting_approval.msg_header')</h1>
    <p class="lead">@lang('messages.sponsor.waiting_approval.msg_lead')</p>
    <p>@lang('messages.sponsor.waiting_approval.msg_body')</p>
@endsection

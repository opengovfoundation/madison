@extends('layouts.app')

@section('pageTitle', trans('messages.sponsor.become'))

@section('content')
    <div class="jumbotron">
        <div class="container">
            <h1>@lang('messages.sponsor.become')</h1>
            <p>@lang('messages.sponsor.info.introtext')</p>
            <p><a class="btn btn-primary btn-lg" href="#get-started" role="button">@lang('messages.get_started') &raquo;</a></p>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <h2 id="get-started">@lang('messages.get_started')</h2>
            <div class="col-md-6">
                <ol class="lead">
                    <li>@lang('messages.sponsor.info.step1', [ 'linkOpen' => '<a href="' . route('register') . '">', 'linkClose' => '</a>' ])</li>
                    <li>@lang('messages.sponsor.info.step2', [ 'linkOpen' => '<a href="' . route('sponsors.create') . '">', 'linkClose' => '</a>' ])</li>
                    <li>@lang('messages.sponsor.info.step3')</li>
                    <li>@lang('messages.sponsor.info.step4')</li>
                    <li>@lang('messages.sponsor.info.step5', [ 'linkOpen' => '<a href="' . route('documents.create') . '">', 'linkClose' => '</a>' ])</li>
                </ol>

                <p><a class="btn btn-primary" href="/madison-mockups/users/sponsors/new.html" role="button">@lang('messages.sponsor.become')</a></p>
            </div>
            <div class="col-md-6">
                <div class="embed-responsive embed-responsive-4by3">
                    <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/69pPKZeKC8U" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
@endsection

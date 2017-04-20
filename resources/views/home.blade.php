@extends('layouts.app')

@section('pageTitle', trans('messages.home.home'))

@push('meta')
    <meta property="og:description" content="{{ trans('messages.home.intro') }}">
@endpush

@section('content')

    @include('home.partials.welcome')

    @if (!$featuredDocuments->isEmpty())
        <h2>@lang('messages.home.featured_title')</h2>
        <ul class="document-grid row featured">
            @foreach ($featuredDocuments->values() as $idx => $document)
                <li class="col-sm-6 col-xs-12">
                    @include('components/document-card', ['document' => $document, 'showIntro' => true])
                </li>

                @if (($idx+1) % 2 == 0)
                    <div class="clearfix visible-sm-block visible-md-block visible-lg-block"></div>
                @endif
            @endforeach
        </ul>
    @endif

    <h2>@lang('messages.home.popular_title')</h2>
    <ul class="document-grid row popular">
        @foreach ($popularDocuments->values() as $idx => $document)
            <li class="col-md-4 col-sm-6 col-xs-12">
                @include('components/document-card', ['document' => $document])
            </li>

            @if (($idx+1) % 3 == 0)
                <div class="clearfix visible-md-block visible-lg-block"></div>
            @endif

            @if (($idx+1) % 2 == 0)
                <div class="clearfix visible-sm-block"></div>
            @endif
        @endforeach
    </ul>
    <div class="row">
        <div class="col-md-12">
            <a href="{{ route('documents.index') }}" class="btn btn-lg btn-primary pull-right">
                @lang('messages.home.all_documents') &raquo;
            </a>
        </div>
    </div>

@endsection

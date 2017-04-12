@extends('layouts.app')

@section('pageTitle', trans('messages.home.home'))

@push('meta')
    <meta property="og:description" content="{{ trans('messages.home.intro') }}">
@endpush

@section('content')

    @include('home.partials.welcome')

    @if (!$featuredDocuments->isEmpty())
        <h2>@lang('messages.home.featured_title')</h2>
        <div class="row featured">
            @each('home.partials.document-card', $featuredDocuments, 'document')
        </div>
    @endif

    <h2>@lang('messages.home.popular_title')</h2>
    <div class="row popular">
        @each('home.partials.document-card', $popularDocuments, 'document')
    </div>
    <div class="row">
        <div class="col-md-12">
            <a href="{{ route('documents.index') }}" class="btn btn-lg btn-primary pull-right">
                @lang('messages.home.all_documents') &raquo;
            </a>
        </div>
    </div>

@endsection

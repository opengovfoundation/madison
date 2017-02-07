@extends('layouts.app')

@section('pageTitle', trans('messages.home.home'))

@section('content')

<div class="row">
    <div class="col-md-6">
        <section class="home-feature">
            @each('home/partials/featured-document', $featuredDocuments, 'document')
        </section>

        @include('home/partials/active-documents', [
            'mostActiveDocuments' => $mostActiveDocuments,
            'mostRecentDocuments' => $mostRecentDocuments,
        ])
    </div>

    <div class="col-md-6">
        @include('home/partials/welcome')
        @include('home/partials/search-list', [
            'documents' => $documents
        ])
    </div>
</div>

@endsection
